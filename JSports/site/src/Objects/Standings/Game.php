<?php

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
