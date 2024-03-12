<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
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
    /**
     * This function is used to actually score a game.  An organization may have different scoring policies.
     * It is expected that this particular functionality can/should be extended to support multiple scoring policies.
     * The default policy is 2 points for a win, 1 point for a tie and 0 points for a loss. 
     * 
     * NOTE:  The scoring policy should be defined at the PROGRAM level.
     * 
     * @todo Enhance this section to support multiple scoring policies
     * 
     * @param GamesTable $game
     */
    public static function scoreGame(GamesTable &$game) {

        if ($game->hometeamscore > $game->awayteamscore) {
            $game->hometeampoints = 2;
            $game->awayteampoints = 0;
        } elseif ($game->awayteamscore > $game->hometeamscore) {
            $game->awayteampoints = 2;
            $game->hometeampoints = 0;
        } else {
            $game->awayteampoints = 1;
            $game->hometeampoints = 1;
        }
        
        return;
    }
}

