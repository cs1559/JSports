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
 * Aggregated season record for a single team, keyed by team ID.
 */
final class TeamRecord
{
    public int $wins = 0;
    public int $losses = 0;
    public int $ties = 0;
    public int $points = 0;
    public int $runsScored = 0;
    public int $runsAllowed = 0;

    /** 1-based standings position, set by StandingsCalculator::calculate(). */
    public int $position = 0;

    /** wins + losses + ties, set by StandingsCalculator::calculate(). */
    public int $gamesPlayed = 0;

    /** (wins + 0.5*ties) / gamesPlayed, set by StandingsCalculator::calculate(). */
    public float $winPct = 0.0;

    /** Games behind the first-place team, set by StandingsCalculator::calculate(). */
    public float $gamesBack = 0.0;

    public function __construct(
        public readonly string $id,
        public string $name,
        public string $coachName = '',
    ) {
    }
}
