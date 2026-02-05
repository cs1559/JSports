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
namespace FP4P\Component\JSports\Site\Services;

/**
 * GameService - This is a service class that exposes certain functions that
 * various components within the applicaiton related to GAMES.
 *
 * REVISION HISTORY:
 * 2025-01-16  Cleaned up code and added inline comments.
 *             Added code to trigger certain events.  
 */

use FP4P\Component\JSports\Administrator\Table\GamesTable;
use FP4P\Component\JSports\Site\Objects\Application;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Objects\Scoring\ScoringEngine;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class GameService
{
    
    /**
     * This function returns an instance of the GameTable class.
     * 
     * @return \FP4P\Component\JSports\Administrator\Table\GamesTable
     */
    public static function getGamesTable() : GamesTable {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        return new GamesTable($db);
    }
    
    
    /**
     * This function will return an individual row based on the GAME ITEM ID
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\GamesTable
     */
    public static function getItem(int $id = 0) : ?GamesTable {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new GamesTable($db);
        
        $row = $table->load($id);
         
        if ($row) {
            return $table;
        }
               
        return null;
    }
    
    /**
     * This function will DELETE a specific row within the GAMES table.
     *
     * @param number $id  Item ID
     * @return bool
     */
    public static function delete(int $id = 0) : bool {
               
        if ($id === 0) {
            LogService::error('Game Record ID ' . $id . ' is required - delete attempted');
            return false;
        }
        
        // @TODO - add rule for when a DELETE operation CANNOT be executed.
        $app = Application::getInstance();
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new GamesTable($db);
        if (!$table->load($id)) {
            LogService::error("Game {$id} not found - delete attempted");
            return false;
        }
        
        try {
            $rc = $table->delete($id);
            
            if ($rc) {
               LogService::info("Game " . $id . " - game [ " . $table->name . "] deleted ");
                $app->triggerEvent('onAfterGameDelete', ['data' => $table, 'returnCode'=> $rc]);
                return true;
            } else {
               LogService::error("Error deleting game " . $id . " - game [ " . $table->name . "] ");
               return false;
            }
        } catch (\Throwable $e) {
            LogService::error("Exception deleting game (" . $id . ") - " . $e->getMessage());
            return false;
        }
//         return $rc;
    }

    
    /**
     * This function will RESET the game status
     *
     * @param number $id  Item ID
     * @return bool
     */
    public static function reset(int $id = 0) : bool {
        
        $params = ComponentHelper::getParams('com_jsports');
        $resetgamescore = $params->get('resetgamescore');
        
        $user = UserService::getUser();
        $app = Application::getInstance();
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new GamesTable($db);
//         $row = $table->load($id);
        if (!$table->load($id)) {
            LogService::error("GameService::reset - Game {$id} not found");
            return false;
        }
        $status = 'S';
        
        // Change the status of the game and identify who actually posted the score.
        if ($resetgamescore) {
            $data = array(
                'gamestatus' => 'S',
                'hometeamscore' => 0,
                'awayteamscore' => 0,
                'hometeampoints' => 0,
                'awayteampoints' => 0,
            );
        } else {
            $data = array(
                'gamestatus' => 'S',
                'hometeampoints' => 0,
                'awayteampoints' => 0,
            );
        }
        
        $table->bind($data);
        
        $table->store();

        $app->triggerEvent('onAfterGameReset', ['data' => $table]);
        
        //LogService::info("Game " . $id . " [" . $table->name . "] - game reset ");
        return true;
    }
    
    
    
    /**
     * The postScore function will update the necessary fields required when a score is posted.
     *
     * @param int $id                   // Game ID
     * @param int $hometeamscore        // Home Team Score
     * @param int $awayteamscore        // Away Team Score
     * @return boolean
     */
    public static function postScore(int $id, int $hometeamscore, int $awayteamscore) : bool {
        
        $user = UserService::getUser();
        $app = Application::getInstance();
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new GamesTable($db);
//         $row = $table->load($id);
        if (!$table->load($id)) {
            LogService::error("GameService::postScore - Game " . $id . " not found");
            return false;
        }
               
        $datetime = date_create()->format('Y-m-d H:i:s');

        // Change the status of the game and identify who actually posted the score.
    	$data = array(
    		'hometeamscore' => $hometeamscore,
    		'awayteamscore' => $awayteamscore,
    		'gamestatus' => 'C',
    	    'updatedby' => $user->username,
    	    'dateupdated' => $datetime,
    	);

    	$table->bind($data);

    	try {
    	    ScoringEngine::scoreGame($table);
    	    
    	} catch (\Throwable $e) {
    	    LogService::error("GameService::postScore Error - " . $e->getMessage());
    	    return false;
    	}
    	
    	$table->store();

    	$app->triggerEvent('onAfterPostScore', ['data' => $table]);
    	
    	return true;
        
    }
    
    /**
     * This function simply returns a "W" or "L" or "T" for a team based on their context (home/away).
     *
     * @param int $teamid
     * @param object $item
     * @return string
     */
    public static function getWinLoss($teamid, $item) : string {
        $result = "-";
//         $_teamid = $teamid;
                
//         if ($item->gamestatus == 'C') {
        if ($item->gamestatus === 'C') {
            if ($teamid == $item->hometeamid) {
                if ($item->hometeamscore > $item->awayteamscore) {
                    $result = "W";
                } else if ($item->hometeamscore < $item->awayteamscore) {
                    $result = "L";
                } else {
                    $result = "T";
                }
            }
            if ($teamid == $item->awayteamid) {
                if ($item->hometeamscore < $item->awayteamscore) {
                    $result = "W";
                } else if ($item->hometeamscore > $item->awayteamscore) {
                    $result = "L";
                } else {
                    $result = "T";
                }
            }
        }
        return $result;
    }
    
    /**
     * This function will retrieve a list of upcoming games that are still "scheduled";
     *
     * @param int $programid
     * @param int $limit
     * @return array<int, object>
     */
    public static function getUpcomingGames(int $programid, int $limit = 25) : array {

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('g.*, d.name as divisionname');
        $query->from($db->quoteName('#__jsports_games') . ' AS g, ' .
            $db->quoteName('#__jsports_divisions') . ' AS d');
        
        $conditions = array(
            $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
            $db->quoteName('g.gamestatus') . ' = "S"',
            $db->quoteName('g.programid') . ' = :programid',
            $db->quoteName('g.gamedate') . ' >= CURRENT_DATE',
        );
        $query->where($conditions);
        $query->setLimit($limit);
        $query->order('g.gamedate asc');
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    /**
     * This function will retrieve a list of upcoming games that are still "scheduled" for a given division;
     *
     * @param int $programid
     * @param int $limit
     * @return array<int, object>
     */
    public static function getUpcomingGamesByDivision(int $programid, int $divisionid, int $limit=300, ?int $teamid = null) : array {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('g.*, d.name as divisionname');
        $query->from($db->quoteName('#__jsports_games') . ' AS g, ' .
            $db->quoteName('#__jsports_divisions') . ' AS d');
        
        if ($divisionid > 0) {
            $conditions = array(
                $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
                $db->quoteName('g.gamestatus') . ' = "S"',
                $db->quoteName('g.programid') . ' = :programid',
                $db->quoteName('g.divisionid') . ' = :divisionid',
                $db->quoteName('g.gamedate') . ' >= CURRENT_DATE',
                 );
                if ($teamid > 0) {
                    $conditions[] = "(" . $db->quoteName('g.hometeamid') . ' = :homeid or '
                        . $db->quoteName('g.awayteamid') . ' = :awayid) ';
                }
        } else {
            $conditions = array(
                $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
                $db->quoteName('g.gamestatus') . ' = "S"',
                $db->quoteName('g.programid') . ' = :programid',
                $db->quoteName('g.gamedate') . ' >= CURRENT_DATE',
            );
        }
        $query->where($conditions);
        $query->setLimit($limit);
        $query->order('g.gamedate asc');
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        if ($divisionid > 0) {
            $query->bind(':divisionid', $divisionid, ParameterType::INTEGER);
        }
        if ($teamid > 0) {
            $query->bind(':homeid', $teamid, ParameterType::INTEGER);
            $query->bind(':awayid', $teamid, ParameterType::INTEGER);
        }
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }

    /**
     * This function will retrieve a list of upcoming games that are still "completed" for a given division;
     *
     * @param int $programid
     * @param int $divisionid
     * @param ?int $teamid
     * @return array<int, object>
     */
    public static function getCompletedGamesByDivision(int $programid, int $divisionid, int $limit = 300, ?int $teamid = null) : array {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('g.*, d.name as divisionname');
        $query->from($db->quoteName('#__jsports_games') . ' AS g, ' .
            $db->quoteName('#__jsports_divisions') . ' AS d');
        
        if ($divisionid > 0) {
            $conditions = array(
                $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
                $db->quoteName('g.gamestatus') . ' = "C"',
                $db->quoteName('g.programid') . ' = :programid',
                $db->quoteName('g.divisionid') . ' = :divisionid',
            );
            
            if ($teamid > 0) {
                 $conditions[] = "(" . $db->quoteName('g.hometeamid') . ' = :homeid or '
                     . $db->quoteName('g.awayteamid') . ' = :awayid) ';
            }
        } else {
            $conditions = array(
                $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
                $db->quoteName('g.gamestatus') . ' = "C"',
                $db->quoteName('g.programid') . ' = :programid',
            );
        }
        $query->where($conditions);
        $query->setLimit($limit);
        $query->order('g.gamedate asc');
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        if ($divisionid > 0) {
            $query->bind(':divisionid', $divisionid, ParameterType::INTEGER);
        }
        if ($teamid > 0) {
            $query->bind(':homeid', $teamid, ParameterType::INTEGER);
            $query->bind(':awayid', $teamid, ParameterType::INTEGER);
        }
        $db->setQuery($query);



        return $db->loadObjectList();
        
    }
    
    /**
     * This function will return a list of most recent games.
     * 
     * @param int $programid
     * @return array<int, object>
     */
    public static function getRecentGames(int $programid, int $limit = 15) : array  {
                
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('g.*, d.name as divisionname');
        $query->from($db->quoteName('#__jsports_games') . ' AS g, ' .
            $db->quoteName('#__jsports_divisions') . ' AS d');
        
        $conditions = array(
            $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
            $db->quoteName('g.gamestatus') . ' = "C"',
            $db->quoteName('g.gamedate') . ' <= now()',
            $db->quoteName('g.programid') . ' = :programid',
        );
        $query->where($conditions);
        $query->setLimit($limit);
        $query->order('g.gamedate desc');
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        $db->setQuery($query);
        return $db->loadObjectList();
       
    }
    
    /**
     * This function will retrieve a team's games for a given program.
     *
     * @param int $teamid
     * @param int $programid
     * @return array<int, object>
     */
    public static function getTeamSchedule(int $teamid, int $programid) : array {
        // Create a new query object.
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select("a.*");
        $query->from($db->quoteName('#__jsports_games') . ' AS a');
        $query->where($db->quoteName('a.programid') . ' = :programid '); 
        $query->where(' (' . $db->quoteName('a.teamid') . ' = :teamid or ' . $db->quoteName('a.opponentid') . ' = :opponentid ) ' );
        $query->order("gamedate asc, gametime asc");
        $query->bind(':programid',$programid, ParameterType::INTEGER);
        $query->bind(':teamid',$teamid, ParameterType::INTEGER);
        $query->bind(':opponentid',$teamid, ParameterType::INTEGER);
        $db->setQuery($query);
        return $db->loadObjectList();
            
    }
}
