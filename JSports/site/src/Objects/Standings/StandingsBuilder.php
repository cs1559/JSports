<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace FP4P\Component\JSports\Site\Objects\Standings;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

/**
 * Joomla 5 standings builder for the JSports component.
 *
 * SCHEMA NOTES:
 * - #__jsports_games has ONE row per game.
 * - #__jsports_map is the authoritative source for team-division membership.
 *   It has two relevant rows per game: one for the home team and one for
 *   the away team. Division membership is determined from this table, NOT
 *   from the divisionid column on the game row itself.
 * - Teams are uniquely identified by ID (not name).
 * - Coach names come from #__jsports_teams.contactname.
 *
 * CROSS-DIVISION GAME STRATEGY:
 *   Home and away stats are aggregated separately via UNION, with the map
 *   table joined on each team's own ID. This means each team's wins/losses/
 *   ties are accumulated in their own division regardless of whether their
 *   opponent is in the same division or not -- exactly matching the
 *   behaviour of the legacy standings SQL.
 *
 *   Head-to-head games (used only for tiebreaker resolution) are fetched
 *   separately and restricted to games where BOTH teams are confirmed
 *   division members.
 */
final class StandingsBuilder
{
    private DatabaseDriver $db;

    public function __construct(?DatabaseDriver $db = null)
    {
        $this->db = $db ?? Factory::getContainer()->get('DatabaseDriver');
    }

    /**
     * @return TeamRecord[] Ordered standings for the division, best team first.
     */
    public function loadStandings(int $divisionId, int $programId): array
    {
        // Step 1 — get pre-aggregated stats per team using UNION approach.
        $statsRows = $this->loadTeamStats($divisionId, $programId);

        if (empty($statsRows)) {
            return [];
        }

        // Step 2 — build TeamRecord objects from the pre-aggregated data.
        $teams = [];
        foreach ($statsRows as $row) {
            $team = new TeamRecord(
                (string) $row->teamid,
                (string) $row->teamname,
                (string) ($row->headcoach ?? ''),
            );
            $team->wins        = (int) ($row->wins        ?? 0);
            $team->losses      = (int) ($row->losses      ?? 0);
            $team->ties        = (int) ($row->ties        ?? 0);
            $team->points      = (int) ($row->points      ?? 0);
            $team->runsScored  = (int) ($row->runsscored  ?? 0);
            $team->runsAllowed = (int) ($row->runsallowed ?? 0);
            $teams[]           = $team;
        }

        // Step 3 — load within-division games for head-to-head tiebreakers.
        $h2hGames = $this->loadDivisionGames($divisionId, $programId);

        // Step 4 — resolve tiebreakers and order standings.
        $calc      = new StandingsCalculator(winPoints: 2, tiePoints: 1, lossPoints: 0);
        $standings = $calc->resolveStandings($teams, $h2hGames);

        // Step 5 — assign position and gamesBack within the division.
        foreach ($standings as $index => $team) {
            $team->position = $index + 1;
        }

        if (!empty($standings)) {
            $leader = $standings[0];
            foreach ($standings as $team) {
                $team->gamesBack = (
                    ($leader->wins - $team->wins) + ($team->losses - $leader->losses)
                ) / 2;
            }
        }

        return $standings;
    }

    /**
     * Aggregates wins, losses, ties, points, and runs per team for the
     * requested division and program using a UNION of home and away games.
     *
     * Joining #__jsports_map on each team's own ID ensures that each game
     * is counted in the correct team's division, even when the opponent is
     * from a different division (cross-division play).
     *
     * @return object[]
     */
    private function loadTeamStats(int $divisionId, int $programId): array
    {
        $pid = (int) $programId;
        $did = (int) $divisionId;
        $p   = $this->db->getPrefix();

        $sql = "
            SELECT
                tempa.teamid,
                tempa.teamname,
                tempa.headcoach,
                COALESCE(tempb.wins,        0) AS wins,
                COALESCE(tempb.losses,      0) AS losses,
                COALESCE(tempb.ties,        0) AS ties,
                COALESCE(tempb.points,      0) AS points,
                COALESCE(tempb.runsscored,  0) AS runsscored,
                COALESCE(tempb.runsallowed, 0) AS runsallowed
            FROM (
                SELECT m.teamid, t.name AS teamname, t.contactname AS headcoach
                FROM      {$p}jsports_map   m
                INNER JOIN {$p}jsports_teams t ON t.id = m.teamid
                WHERE m.programid  = {$pid}
                  AND m.divisionid = {$did}
                  AND m.published  = 1
            ) tempa
            LEFT JOIN (
                SELECT teamid,
                       SUM(wins)        AS wins,
                       SUM(losses)      AS losses,
                       SUM(ties)        AS ties,
                       SUM(points)      AS points,
                       SUM(runsscored)  AS runsscored,
                       SUM(runsallowed) AS runsallowed
                FROM (
                    -- Home games: accumulate results for the home team in their division
                    SELECT
                        g.hometeamid AS teamid,
                        SUM(IF(g.hometeamscore >  g.awayteamscore, 1, 0)) AS wins,
                        SUM(IF(g.hometeamscore <  g.awayteamscore, 1, 0)) AS losses,
                        SUM(IF(g.hometeamscore =  g.awayteamscore, 1, 0)) AS ties,
                        SUM(IF(g.hometeamscore >  g.awayteamscore, 2,
                            IF(g.hometeamscore =  g.awayteamscore, 1, 0))) AS points,
                        SUM(g.hometeamscore) AS runsscored,
                        SUM(g.awayteamscore) AS runsallowed
                    FROM {$p}jsports_games g
                    INNER JOIN {$p}jsports_map m
                        ON  m.teamid    = g.hometeamid
                        AND m.programid = g.programid
                        AND m.divisionid = {$did}
                        AND m.published  = 1
                    WHERE g.programid  = {$pid}
                      AND g.gamestatus = 'C'
                      AND g.leaguegame = 1
                    GROUP BY g.hometeamid

                    UNION ALL

                    -- Away games: accumulate results for the away team in their division
                    SELECT
                        g.awayteamid AS teamid,
                        SUM(IF(g.awayteamscore >  g.hometeamscore, 1, 0)) AS wins,
                        SUM(IF(g.awayteamscore <  g.hometeamscore, 1, 0)) AS losses,
                        SUM(IF(g.hometeamscore =  g.awayteamscore, 1, 0)) AS ties,
                        SUM(IF(g.awayteamscore >  g.hometeamscore, 2,
                            IF(g.hometeamscore =  g.awayteamscore, 1, 0))) AS points,
                        SUM(g.awayteamscore) AS runsscored,
                        SUM(g.hometeamscore) AS runsallowed
                    FROM {$p}jsports_games g
                    INNER JOIN {$p}jsports_map m
                        ON  m.teamid    = g.awayteamid
                        AND m.programid = g.programid
                        AND m.divisionid = {$did}
                        AND m.published  = 1
                    WHERE g.programid  = {$pid}
                      AND g.gamestatus = 'C'
                      AND g.leaguegame = 1
                    GROUP BY g.awayteamid
                ) game_stats
                GROUP BY teamid
            ) tempb ON tempa.teamid = tempb.teamid
        ";

        $this->db->setQuery($sql);

        return $this->db->loadObjectList();
    }

    /**
     * Loads individual game results restricted to games where BOTH teams
     * are confirmed members of the division per #__jsports_map.
     *
     * These are used exclusively for head-to-head tiebreaker resolution
     * inside StandingsCalculator -- not for computing W/L/T totals.
     *
     * @return Game[]
     */
    private function loadDivisionGames(int $divisionId, int $programId): array
    {
        $pid = (int) $programId;
        $did = (int) $divisionId;
        $p   = $this->db->getPrefix();

        $sql = "
            SELECT g.hometeamid, g.awayteamid, g.hometeamscore, g.awayteamscore
            FROM {$p}jsports_games g
            WHERE g.programid  = {$pid}
              AND g.gamestatus = 'C'
              AND g.leaguegame = 1
              AND EXISTS (
                  SELECT 1 FROM {$p}jsports_map m
                  WHERE m.teamid    = g.hometeamid
                    AND m.programid  = {$pid}
                    AND m.divisionid = {$did}
                    AND m.published  = 1
              )
              AND EXISTS (
                  SELECT 1 FROM {$p}jsports_map m
                  WHERE m.teamid    = g.awayteamid
                    AND m.programid  = {$pid}
                    AND m.divisionid = {$did}
                    AND m.published  = 1
              )
        ";

        $this->db->setQuery($sql);
        $rows = $this->db->loadObjectList();

        return array_map(
            static fn (object $row) => new Game(
                (string) $row->hometeamid,
                (string) $row->awayteamid,
                (int)    $row->hometeamscore,
                (int)    $row->awayteamscore,
            ),
            $rows
        );
    }
}
