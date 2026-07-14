<?php

/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace FP4P\Component\JSports\Site\Objects\Standings;

/**
 * Youth Baseball League Standings Calculator
 *
 * Tiebreaker order (when teams are tied on points):
 *   1. Head-to-head record (points earned in games against each other)
 *   2. Head-to-head runs allowed (fewest, in games against each other)
 *   3. Head-to-head runs scored (most, in games against each other)
 *   4. Total runs allowed for the season (fewest)
 *
 * Ties are broken recursively: if a tiebreaker splits a tied group into
 * smaller groups that are still tied among themselves, the next
 * tiebreaker is applied only within those smaller groups.
 *
 * Teams are uniquely identified by ID (not name), since display names
 * are not guaranteed to be unique.
 */
final class StandingsCalculator
{
    /** @var Game[] */
    private array $games = [];

    /** @var array<string, string> teamId => display name (last name seen wins) */
    private array $teamNames = [];

    /** @var array<string, string> teamId => coach name (last name seen wins) */
    private array $coachNames = [];

    /** Points awarded per outcome. Adjust to match your league's rules. */
    public function __construct(
        private readonly int $winPoints = 2,
        private readonly int $tiePoints = 1,
        private readonly int $lossPoints = 0,
    ) {
    }

    public function addGame(
        string $homeId,
        string $homeName,
        string $awayId,
        string $awayName,
        int $homeScore,
        int $awayScore,
        string $homeCoachName = '',
        string $awayCoachName = '',
    ): void {
        $this->games[] = new Game($homeId, $awayId, $homeScore, $awayScore);
        $this->teamNames[$homeId] = $homeName;
        $this->teamNames[$awayId] = $awayName;

        if ($homeCoachName !== '') {
            $this->coachNames[$homeId] = $homeCoachName;
        }
        if ($awayCoachName !== '') {
            $this->coachNames[$awayId] = $awayCoachName;
        }
    }

    /**
     * Resolves standings from pre-aggregated TeamRecord objects.
     *
     * Use this (instead of addGame/calculate) when team stats have already
     * been computed via SQL (e.g., the UNION home/away approach needed for
     * cross-division games). The $h2hGames array should contain only games
     * between teams within the same division, used solely for tiebreaker
     * resolution.
     *
     * @param TeamRecord[] $teams    Pre-built records with wins/losses/ties/points/runs set.
     * @param Game[]       $h2hGames Within-division game results for tiebreaker use only.
     * @return TeamRecord[]
     */
    public function resolveStandings(array $teams, array $h2hGames): array
    {
        $this->games = $h2hGames;

        foreach ($teams as $team) {
            $this->teamNames[$team->id]  = $team->name;
            $this->coachNames[$team->id] = $team->coachName;

            $team->gamesPlayed = $team->wins + $team->losses + $team->ties;
            $team->winPct      = $team->gamesPlayed > 0
                ? ($team->wins + 0.5 * $team->ties) / $team->gamesPlayed
                : 0.0;
        }

        $byPoints = [];
        foreach ($teams as $team) {
            $byPoints[$team->points][] = $team;
        }
        krsort($byPoints);

        $ordered = [];
        foreach ($byPoints as $group) {
            array_push($ordered, ...$this->resolveGroup($group));
        }

        return $ordered;
    }

    /**
     * @return TeamRecord[] Ordered standings, best team first.
     */
    public function calculate(): array
    {
        $teams = $this->buildBaseRecords();

        // Sort by points first, then resolve ties group by group.
        $byPoints = [];
        foreach ($teams as $team) {
            $byPoints[$team->points][] = $team;
        }
        krsort($byPoints); // highest points first

        $ordered = [];
        foreach ($byPoints as $group) {
            $resolved = $this->resolveGroup($group);
            array_push($ordered, ...$resolved);
        }

        foreach ($ordered as $index => $team) {
            $team->gamesPlayed = $team->wins + $team->losses + $team->ties;
            $team->winPct = $team->gamesPlayed > 0
                ? ($team->wins + 0.5 * $team->ties) / $team->gamesPlayed
                : 0.0;
        }

        return $ordered;
    }

    /**
     * @return TeamRecord[]
     */
    private function buildBaseRecords(): array
    {
        /** @var array<string, TeamRecord> $teams */
        $teams = [];

        $get = function (string $id) use (&$teams): TeamRecord {
            return $teams[$id] ??= new TeamRecord(
                $id,
                $this->teamNames[$id] ?? $id,
                $this->coachNames[$id] ?? '',
            );
        };

        foreach ($this->games as $game) {
            $home = $get($game->homeId);
            $away = $get($game->awayId);

            $home->runsScored += $game->homeScore;
            $home->runsAllowed += $game->awayScore;
            $away->runsScored += $game->awayScore;
            $away->runsAllowed += $game->homeScore;

            if ($game->homeScore > $game->awayScore) {
                $home->wins++;
                $home->points += $this->winPoints;
                $away->losses++;
                $away->points += $this->lossPoints;
            } elseif ($game->awayScore > $game->homeScore) {
                $away->wins++;
                $away->points += $this->winPoints;
                $home->losses++;
                $home->points += $this->lossPoints;
            } else {
                $home->ties++;
                $away->ties++;
                $home->points += $this->tiePoints;
                $away->points += $this->tiePoints;
            }
        }

        return array_values($teams);
    }

    /**
     * Resolves a group of teams tied on points using the tiebreaker
     * hierarchy, recursing into sub-groups as needed.
     *
     * @param TeamRecord[] $group
     * @return TeamRecord[]
     */
    private function resolveGroup(array $group): array
    {
        if (count($group) <= 1) {
            return $group;
        }

        $ids = array_map(fn (TeamRecord $t) => $t->id, $group);
        $h2hGames = $this->gamesAmong($ids);

        // Tiebreaker 1: head-to-head points among the tied teams.
        $h2hPoints = $this->headToHeadPoints($ids, $h2hGames);
        $sorted = $this->splitAndSort($group, fn (TeamRecord $t) => $h2hPoints[$t->id]);
        if ($this->hasResolvedOrder($sorted)) {
            return $this->finishOrRecurse($sorted);
        }

        // Tiebreaker 2: head-to-head runs allowed (fewest is better), among the tied teams.
        $h2hRunsAllowed = $this->headToHeadRuns($ids, $h2hGames, allowed: true);
        $sorted = $this->splitAndSort($group, fn (TeamRecord $t) => -$h2hRunsAllowed[$t->id]);
        if ($this->hasResolvedOrder($sorted)) {
            return $this->finishOrRecurse($sorted);
        }

        // Tiebreaker 3: head-to-head runs scored (most is better), among the tied teams.
        $h2hRunsScored = $this->headToHeadRuns($ids, $h2hGames, allowed: false);
        $sorted = $this->splitAndSort($group, fn (TeamRecord $t) => $h2hRunsScored[$t->id]);
        if ($this->hasResolvedOrder($sorted)) {
            return $this->finishOrRecurse($sorted);
        }

        // Tiebreaker 4: total season runs allowed (fewest is better).
        $sorted = $this->splitAndSort($group, fn (TeamRecord $t) => -$t->runsAllowed);

        // Whatever remains tied after all tiebreakers stays in a stable
        // (name-based) order rather than being arbitrary.
        return $this->finalizeRemainingTies($sorted);
    }

    /**
     * @param TeamRecord[] $group
     * @param callable(TeamRecord): int $scoreFn Higher score = better.
     * @return TeamRecord[][] List of sub-groups, still in best-to-worst order.
     */
    private function splitAndSort(array $group, callable $scoreFn): array
    {
        $scored = [];
        foreach ($group as $team) {
            $scored[] = [$team, $scoreFn($team)];
        }
        usort($scored, fn ($a, $b) => $b[1] <=> $a[1]);

        $subGroups = [];
        $lastScore = null;
        foreach ($scored as [$team, $score]) {
            if ($score !== $lastScore) {
                $subGroups[] = [];
                $lastScore = $score;
            }
            $subGroups[count($subGroups) - 1][] = $team;
        }

        return $subGroups;
    }

    /**
     * @param TeamRecord[][] $subGroups
     */
    private function hasResolvedOrder(array $subGroups): bool
    {
        // Resolved further if this tiebreaker split the original group
        // into more than one sub-group (even if some sub-groups still
        // contain ties, that's progress -- it gets handled recursively).
        return count($subGroups) > 1;
    }

    /**
     * @param TeamRecord[][] $subGroups
     * @return TeamRecord[]
     */
    private function finishOrRecurse(array $subGroups): array
    {
        $result = [];
        foreach ($subGroups as $subGroup) {
            array_push($result, ...$this->resolveGroup($subGroup));
        }
        return $result;
    }

    /**
     * @param TeamRecord[][] $subGroups
     * @return TeamRecord[]
     */
    private function finalizeRemainingTies(array $subGroups): array
    {
        $result = [];
        foreach ($subGroups as $subGroup) {
            usort($subGroup, fn (TeamRecord $a, TeamRecord $b) => $a->name <=> $b->name);
            array_push($result, ...$subGroup);
        }
        return $result;
    }

    /**
     * @param string[] $ids
     * @return Game[] Games where both teams are in $ids.
     */
    private function gamesAmong(array $ids): array
    {
        $set = array_flip($ids);
        return array_values(array_filter(
            $this->games,
            fn (Game $g) => isset($set[$g->homeId]) && isset($set[$g->awayId])
        ));
    }

    /**
     * @param string[] $ids
     * @param Game[] $games
     * @return array<string, int>
     */
    private function headToHeadPoints(array $ids, array $games): array
    {
        $points = array_fill_keys($ids, 0);

        foreach ($games as $game) {
            if ($game->homeScore > $game->awayScore) {
                $points[$game->homeId] += $this->winPoints;
                $points[$game->awayId] += $this->lossPoints;
            } elseif ($game->awayScore > $game->homeScore) {
                $points[$game->awayId] += $this->winPoints;
                $points[$game->homeId] += $this->lossPoints;
            } else {
                $points[$game->homeId] += $this->tiePoints;
                $points[$game->awayId] += $this->tiePoints;
            }
        }

        return $points;
    }

    /**
     * @param string[] $ids
     * @param Game[] $games
     * @return array<string, int>
     */
    private function headToHeadRuns(array $ids, array $games, bool $allowed): array
    {
        $runs = array_fill_keys($ids, 0);

        foreach ($games as $game) {
            if ($allowed) {
                $runs[$game->homeId] += $game->awayScore;
                $runs[$game->awayId] += $game->homeScore;
            } else {
                $runs[$game->homeId] += $game->homeScore;
                $runs[$game->awayId] += $game->awayScore;
            }
        }

        return $runs;
    }
}
