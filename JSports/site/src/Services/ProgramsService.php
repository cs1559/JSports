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

use FP4P\Component\JSports\Administrator\Table\ProgramsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Services\SecurityService;

class ProgramsService
{
    
    /**
     * This function will return an individual row based on the PROGRAM ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\ProgramsTable|NULL
     */
    public static function getItem($id = 0) {
        
        $db = Factory::getDbo();
        $programs = new ProgramsTable($db);
                
        $row = $programs->load($id);
        
        if ($row) {
            return $programs;
        }
               
        return null;
    }
    
    /**
     * This function will return an array of objects that represent a list of programs that have not
     * been completed.
     *
     * @return unknown
     */
    public static function getNonCompletedPrograms($activeonly = false) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.status') . ' <> "C"',
            $db->quoteName('p.published') . ' in (1) '
        );
        if ($activeonly) {
            $conditions[] = $db->quoteName('p.status') . ' = "A"';
        }
        $query->where($conditions);
        $query->order('id asc');
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    /**
     * This function will return a default program.  The default program will be the most recent
     * non-completed program.
     *
     * @return unknown
     */
    public static function getDefaultProgram() {
        
        $programs = ProgramsService::getNonCompletedPrograms();
        
        return $programs[0];
    }
    
    
    public static function getProgramList() {
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.published') . ' in (0,1) '
        );
        $query->where($conditions);
        $query->order("name desc");
        $db->setQuery($query);
        return $db->loadAssocList();
        
        
    }
    
    
    public static function getMostRecentProgram() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.published') . ' in (1) '
        );
        $query->where($conditions);
        $query->order("id desc");
        $db->setQuery($query);
        $rows = $db->loadAssocList();
        return $rows[0];
        
    }
    
    
    public static function closeProgram($programid) {

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $returncode = 0;
        
        // Check authorization to close out program.
         if (!SecurityService::isAdmin()) {
             Factory::getApplication()->enqueueMessage("User is NOT authorized", 'warning');
             return false;
         }

        $table = new ProgramsTable($db);
        $row = $table->load($programid);
        
        if ($table->status == "C") {
            Factory::getApplication()->enqueueMessage("Program already CLOSED", 'warning');
            return false;
        }
        
        
//         // INSERT CURRENT STANDINGS INTO PAST STANDINGS (MIGRATE)
        $query = $db->getQuery(true);
        $sql = "delete from #__jsports_test_standings
                    where programid = " . $db->quote($programid);
        
        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();
        
        $query = $db->getQuery(true);
        $sql = "insert into " . $db->quoteName("#__jsports_test_standings")
                . " select * from " . $db->quoteName("#__jsports_standings")  
                . " where programid = " . $db->quote($programid);

        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();

        $query = $db->getQuery(true);
        $sql = "delete from #__jsports_recordhistory
                    where programid = " . $db->quote($programid);
        
        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();
        
//         // Create new Record History record
//         /*
//          * Pull the data from the current standings or from the past standings for the specific programid and 
//          * create the new record history.
        $query = $db->getQuery(true);
        $sql = "insert into #__jsports_recordhistory
                select teamid, s.programid, p.name as programname, d.id as divisionid, 
                    d.name as divisionname , teamname, runsscored, runsallowed, wins, 
                    losses, ties, points
                from #__jsports_standings s, #__jsports_programs p, #__jsports_divisions d
                where s.programid = p.id
                    and s.divisionid = d.id
                    and s.programid = " . $db->quote($programid);
        
//          */
        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();
        
        
        echo "UPDATING THE PROGRAM RECORD <br/>";
//         // UPDATE THE PROGRAM RECORD
//         // Change program status to CLOSED
//         // Set registration flag to OFF
//         // Set ACTIVE falg to OFF
//         // Delete records from the current standings table.
//         // Save the record
        
         $table = new ProgramsTable($db);
         $row = $table->load($programid);
        
//         // Change the status of the game and identify who actually posted the score.
        $data = array(
            'status' => 'C',
            'active' => 0,
            'registrationopen' => 0,
        );
        
        $table->bind($data);       
        $table->store();

        
        echo "DELETING CURRENT STANDINGS <br/>";
        //         // DELETE CURRENT STANDINGS
        //         $query = $db->getQuery(true);
        //         $conditions = array(
        //             $db->quoteName('programid') . ' = ' . $db->quoteName($programid),
        //             $db->quoteName('1'),
        //         );
        //         $query->delete($db->quoteName('#__jsports_standings'));
        //         $query->where($conditions);
        //         $db->setQuery($query);
        //         $result = $db->execute();
        

        return true;
        
    }

    
    
    
    
    
    public static function rollbackProgram($programid) {
        
        // Check authorization to close out program.
        //         if (!SecurityService::isAdmin()) {
        //             throw new \Exception('Not Authorized');
        //         }
        
        echo "DELETING STANDINGS FROM PAST STANDINGS TABLE<br/>";
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        
        //         // INSERT CURRENT STANDINGS INTO PAST STANDINGS (MIGRATE)
        $sql = "delete from " . $db->quoteName("#__jsports_test_standings")
        . " where programid = " . $db->quote($programid);
        
        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();
        
        echo "++ " . $result . "<br/>";
        
        
        echo "DELETE RECORD HISTORY ENTRY <br/>";
        //         // Create new Record History record
        //         /*
        //          * Pull the data from the current standings or from the past standings for the specific programid and
        //          * create the new record history.
        $query = $db->getQuery(true);
        $sql = "delete from #__jsports_recordhistory
                    and programid = " . $db->quote($programid);
        
        //          */
        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();
        
        echo "++ " . $result . "<br/>";
        
        
        
        echo "UPDATING THE PROGRAM RECORD <br/>";
        //         // UPDATE THE PROGRAM RECORD
        //         // Change program status to CLOSED
        //         // Set registration flag to OFF
        //         // Set ACTIVE falg to OFF
        //         // Delete records from the current standings table.
        //         // Save the record
        
        $table = new ProgramsTable($db);
        $row = $table->load($programid);
        
        //         // Change the status of the game and identify who actually posted the score.
        $data = array(
            'status' => 'S',
            'active' => 1,
            'registrationopen' => 0,
        );
        
        $table->bind($data);
        $table->store();
        
        
        echo "DELETING CURRENT STANDINGS <br/>";
        //         // DELETE CURRENT STANDINGS
        //         $query = $db->getQuery(true);
        //         $conditions = array(
        //             $db->quoteName('programid') . ' = ' . $db->quoteName($programid),
        //             $db->quoteName('1'),
        //         );
        //         $query->delete($db->quoteName('#__jsports_standings'));
        //         $query->where($conditions);
        //         $db->setQuery($query);
        //         $result = $db->execute();
        
        
    }
    
}

