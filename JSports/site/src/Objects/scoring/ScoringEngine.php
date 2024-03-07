<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Objects\Scoring;

use FP4P\Component\JSports\Administrator\Table\GamesTable;

class ScoringEngine 
{
    public static function scoreGame(GamesTable &$game) {

        if ($game->hometeamscore > $game->awayteamscore) {
            $game->hometeampoints = 2;
            $game->awayteampoints = 0;
        } elseif ($game->awayteamscore > $game->hometeamscore) {
            $game->awayteampoints = 2;
            $game->hometeampoints = 0;
        } else {
            $game->awayteampoonts = 1;
            $game->hometeampoints = 1;
        }
        
        return;
    }
}

