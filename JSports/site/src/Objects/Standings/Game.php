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
 * A single completed game between two teams, identified by team ID
 * (not name), since display names are not guaranteed to be unique.
 */
final class Game
{
    public function __construct(
        public readonly string $homeId,
        public readonly string $awayId,
        public readonly int $homeScore,
        public readonly int $awayScore,
    ) {
    }
}
