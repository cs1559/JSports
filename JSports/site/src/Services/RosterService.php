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

namespace FP4P\Component\JSports\Site\Services;

use FP4P\Component\JSports\Administrator\Table\RostersTable;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;

class RosterService
{
    
    /**
     * Function just to return the correct ROSTERS database table.
     * 
     * @return RostersTable
     */
    public static function getRostersTable() : RostersTable {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        return new RostersTable($db);
    }
    
    
    /**
     * This function will return an individual row based on the ROSTER ITEM ID
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\RostersTable
     */
    public function getItem(int $id = 0) : ?RostersTable {
        
//         $db = Factory::getDbo();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new RostersTable($db);
        
        $row = $table->load($id);
        
        if ($row) {
            return $table;
        }
               
        return null;
    }
    
    /**
     * This function will DELETE a specific row within the ROSTERS table.
     *
     * @param number $id  Item ID
     * @return bool
     */
    public static function delete(int $id = 0) : bool {
        
        $logger = Myapp::getLogger();
        
        if ($id === 0) {
            $logger->error('Roster Record ID ' . $id . ' is required ');
            return false;
        }
        $svc = new RosterService();
        $item = $svc->getItem($id);
        if ($item === null) {
            $logger->error("Roster record not found (id=$id)");
            return false;
        }
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        $query = $db->getQuery(true);
        
        $conditions = array(
            $db->quoteName('id') . ' = :id' //.$db->quote($id));
        );
        $query->delete($db->quoteName('#__jsports_rosters'));
        $query->where($conditions);
        $query->bind(':id', $id, ParameterType::INTEGER);
        
        $db->setQuery($query);
        
        try {
            $db->execute();
            $logger->info('Deleting roster item - ' . $item->firstname . ' ' . $item->lastname);
            return true;
        } catch (\Throwable $e) {
            $logger->error('Roster delete failed - Record ID ' . $id . ' - ' . $e->getMessage());
            return false;
        }

    }
      
    
    /**
     * Function to retrieve STAFF members from the roster.  this function passes a
     * hard coded value of 'S' to filter the roster list.
     * 
     * @param number $teamid
     * @param number $programid
     * @return array<object>
     */
    public static function getRosterStaff($teamid, $programid) {
        return RosterService::getRosterDataByType($teamid, $programid, 'S');
    }

    /**
     * This function retrieves a list of PLAYERS from the roster.  The function uses a 
     * hard coded value of 'P' ot filter the roster list.  Additionally, one of the input 
     * parameters is a boolean that indicates of the return array should include 
     * substitute players on the roster.
     * 
     * @param number $teamid
     * @param number $programid
     * @param boolean $includesubs
     * @return array
     */
    public static function getRosterPlayers($teamid, $programid, $includesubs = true) {
        return RosterService::getRosterDataByType($teamid, $programid, 'P', $includesubs);
    }
    
    
    /**
     * This is a private function used to retrieve roster items filtered based on the input
     * parameters.
     * 
     * @param number $teamid
     * @param number $programid
     * @param String $classification
     * @param boolean $includesubs
     * @return array<object>
     */
    private static function getRosterDataByType($teamid, $programid, $classification, $includesubs = true) : array {
//         $db = Factory::getDbo();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
              
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_rosters') . ' AS p ');
        
        /* If the results should filter out substitute players */
        if ($includesubs) {      
            $conditions = array(
                $db->quoteName('p.classification') . ' = :classification',
                $db->quoteName('p.teamid') . ' = :teamid', 
                $db->quoteName('p.programid') . ' = :programid'
            );
        } else {
            $conditions = array(
                $db->quoteName('p.classification') . ' = :classification',
                $db->quoteName('p.teamid') . ' = :teamid',
                $db->quoteName('p.programid') . ' = :programid',
                $db->quoteName('p.substitute') . ' = 0'
            );
        }
        $query->where($conditions);
        $query->bind(':teamid', $teamid, ParameterType::INTEGER);
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        $query->bind(':classification', $classification, ParameterType::STRING);
        
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    /**
     * This function returns the total player count for a teams roster for a given program/season.
     * 
     * @param number $teamid
     * @param number $programid
     * @param boolean $includesubs
     * @return int
     */
    public static function getPlayerCount($teamid, $programid, $includesubs = true) : int {
        return count(RosterService::getRosterPlayers($teamid, $programid, $includesubs));
    }
    
    /**
     * This is a roster function that checks to see if the program/season requires
     * roster limits and compares current size to max roster size and returns a boolean.
     * 
     * @param number $teamid
     * @param number $programid
     * @return boolean
     */
    public static function canAddPlayers($teamid, $programid) : bool {
        $program = ProgramsService::getItem($programid);
        
        $currentplayers = RosterService::getPlayerCount($teamid, $programid, $program->includesubstitutes);
        
        $limitroster = $program->limitroster;
        $rostersize = $program->rostersize;
        
        if (!$program->limitroster) {
            return true;
        }
        
        if ($rostersize <= $currentplayers) {
            return false;
        }
        
        return true;
    }
    
}
