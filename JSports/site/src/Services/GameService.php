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

use FP4P\Component\JSports\Administrator\Table\GamesTable;
use FP4P\Component\JSports\Site\Objects\Application;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Scoring\ScoringEngine;

class GameService
{
    
    public static function getGamesTable() {
        $db = Factory::getDbo();
        return new GamesTable($db);
    }
    
    
    /**
     * This function will return an individual row based on the GAME ITEM ID
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\GamesTable
     */
    public static function getItem($id = 0) {
        
        $db = Factory::getDbo();
        $table = new GamesTable($db);
        
        $item = null;
        
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
     */
    public static function delete($id = 0) {
        
        // @TODO - add rule for when a DELETE operation CANNOT be executed.
        $app = Application::getInstance();
        
        $db = Factory::getDbo();
        $table = new GamesTable($db);
        $row = $table->load($id);
        
        $rc = $table->delete();
        
        $app->triggerEvent('onAfterGameDelete', ['data' => $table, 'returnCode'=> $rc]);
        
        return $rc;
    }

    
    /**
     * This function will RESET the game status
     *
     * @param number $id  Item ID
     */
    public static function reset($id = 0) {
        
        $user = Factory::getUser();
        $app = Application::getInstance();
        
        $db = Factory::getDbo();
        $table = new GamesTable($db);
        $row = $table->load($id);
        
        $status = 'S';
        
        // Change the status of the game and identify who actually posted the score.
        $data = array(
            'gamestatus' => 'S',
//             'updatedby' => $user->username,
//             'dateupdated' => $datetime,
        );
        
        $table->bind($data);
        
        $table->store();
        
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
    public static function postScore($id, $hometeamscore, $awayteamscore) {
        
        $user = Factory::getUser();
        $app = Application::getInstance();
        
        $db = Factory::getDbo();
        $table = new GamesTable($db);
        $row = $table->load($id);
               
        $datetime = date_create()->format('Y-m-d H:i:s');

        // Change the status of the game and identify who actually posted the score.
    	$data = array(
    		'hometeamscore' => $db->quote($hometeamscore),
    		'awayteamscore' => $db->quote($awayteamscore),
    		'gamestatus' => $db->quote('C'),
    	    'updatedby' => $db->quote($user->username),
    	    'dateupdated' => $db->quote($datetime),
    	);

    	$table->bind($data);

    	try {
    	    ScoringEngine::scoreGame($table);
    	    
    	} catch (Exception $e) {
    	    echo "GameService::postScore <br/>";
    	    return false;
    	}
    	
    	$table->store();

    	$app->triggerEvent('onAfterPostScore', ['data' => $table]);
    	
    	return true;
        
    }
    
    /**
     * This function simply returns a "W" or "L" or "T" for a team based on their context (home/away).
     *
     * @param unknown $teamid
     * @param unknown $item
     * @return string
     */
    public static function getWinLoss($teamid, $item) {
        $result = "-";
        $_teamid = $teamid;
                
        if ($item->gamestatus == 'C') {
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
     * @param unknown $programid
     * @param number $limit
     * @return unknown
     */
    public static function getUpcomingGames($programid = null, $limit = 25) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('g.*, d.name as divisionname');
        $query->from($db->quoteName('#__jsports_games') . ' AS g, ' .
            $db->quoteName('#__jsports_divisions') . ' AS d');
        
        $conditions = array(
            $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
            $db->quoteName('g.gamestatus') . ' = "S"',
            $db->quoteName('g.programid') . ' = ' . $db->quote($programid),
        );
        $query->where($conditions);
        $query->setLimit($limit);
        $query->order('g.gamedate asc');
        $db->setQuery($query);
        return $db->loadObjectList();
       
        
    }
    
    
    /**
     * This function will return a list of most recent games.
     * @param unknown $programid
     * @return array Objects
     */
    public static function getRecentGames($programid = null, $limit = 15) {
                
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('g.*, d.name as divisionname');
        $query->from($db->quoteName('#__jsports_games') . ' AS g, ' .
            $db->quoteName('#__jsports_divisions') . ' AS d');
        
        $conditions = array(
            $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'),
            $db->quoteName('g.gamestatus') . ' = "C"',
            $db->quoteName('g.gamedate') . ' <= now()',
            $db->quoteName('g.programid') . ' = ' . $db->quote($programid),
        );
        $query->where($conditions);
        $query->setLimit($limit);
        $query->order('g.gamedate desc');
        $db->setQuery($query);
        return $db->loadObjectList();
       
    }
    
    /**
     * This function will retrieve a team's games for a given program.
     *
     * @param unknown $teamid
     * @param unknown $programid
     * @return unknown
     */
    public static function getTeamSchedule($teamid, $programid) {
        // Create a new query object.
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select("a.*");
        $query->from($db->quoteName('#__jsports_games') . ' AS a')
        ->where($db->quoteName('a.programid') . ' = ' . $db->quote($programid))
        ->where(" (" . $db->quoteName('a.teamid') . ' = ' . $db->quote($teamid)
            . " or " . $db->quoteName('a.opponentid') . ' = ' . $db->quote($teamid) . ")")
        ->order("gamedate asc");
        $db->setQuery($query);
        return $db->loadObjectList();
            
    }
}
