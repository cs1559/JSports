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

/**
 * CHANGE LOG:
 * 02/13/2024 - Added CONTACT NAME to the option value in the select element.  Also added OPTION GROUPS for cross-divisional play.
 */
    namespace FP4P\Component\JSports\Site\Services;
    
    use FP4P\Component\JSports\Administrator\Table\TeamsTable;
    use Joomla\Database\ParameterType;
    use Joomla\CMS\Factory;
    use FP4P\Component\JSports\Site\Services\DivisionService;
    
    
    class TeamService
    {
        
        public static function getTeamsTable() {
            $db = Factory::getDbo();
            $teams = new TeamsTable($db);
            return $teams;
        }
        
        
        /**
         * This function will return an individual row based on the Team ID.
         * 
         * @param number $id
         * @return FP4P\Component\JSports\Administrator\Table\TeamsTable|NULL
         */
        public static function getItem($id = 0) {
            
            $db = Factory::getDbo();
            $team = new TeamsTable($db);
            
            $item = null;
            
            $row = $team->load($id);
            
            
            if ($row) {
                return $team;
            }
                   
            return null;
        }
        
        public function updateTeamLogoFilename($teamid, $filename) {
            
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            
            $fields = array(
                $db->quoteName('logo') . '= :filename_value'
            );
            
            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = :teamid'
            );
            
            $query->update($db->quoteName('#__jsports_teams'))->set($fields)->where($conditions);
            
            $query->bind(':teamid', $teamid, ParameterType::INTEGER)
                ->bind(':filename_value', $filename, ParameterType::STRING);
            
            $db->setQuery($query);
                
            $result = $db->execute();
            
            return $result;
            
        }
        
        
        
        public function getMostRecentProgram($teamid) {
        
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
                    
            // Select the required fields from the table.
    //         $query->select($db->quoteName(array('lastplayed', 'lastprogramid')));
    //         $query->from($db->quoteName('#__jsports_view_lastplayed'));
    //         $query->where($db->quoteName('teamid') . ' = :id' );
    //         $query->bind(':id', $teamid, ParameterType::INTEGER);
    
    //         $query->select($db->quoteName(array('max(programid)')));
    //         $query->from($db->quoteName('#__jsports_map'));
    //         $query->where($db->quoteName('teamid') . ' = :id' );
    //         $query->bind(':id', $teamid, ParameterType::INTEGER);
    
            $query="SELECT max(programid) as lastprogramid FROM `#__jsports_map` WHERE teamid = " . $teamid;
            
            $db->setQuery($query);
            
            // Load the results as a list of stdClass objects (see later for more options on retrieving data).
            $row = $db->loadAssoc();
    
            return $row;        
        }
        
        
        public static function getTeamDivisionId($teamid, $programid) {
            
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            
            $query->select($db->quoteName(array('teamid', 'programid','divisionid')));
            $query->from($db->quoteName('#__jsports_map'));
            
            $conditions = array(
                $db->quoteName('teamid') . ' = ' . $db->quote($teamid),
                $db->quoteName('programid') . ' = ' . $db->quote($programid),
            );
            
            $query->where($conditions);
            $db->setQuery($query);
            
            $row = $db->loadAssoc();
       
            
            return $row['divisionid'];            
        }
        
        
        
        
        public static function getTeamList($teamid, $programid, $divisionid = null) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            
            if (is_null($divisionid)) {
                $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, d.agegroup, a.contactname FROM " .
                    $db->quoteName('#__jsports_teams') . " as a, " .
                    $db->quoteName('#__jsports_map') . " as m, " .
                    $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid in (
                    select divisionid from " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                    where m.divisionid = d.id
                    and m.teamid = " . $db->quote($teamid) . " and m.programid = " . $db->quote($programid) . "
                    )
		          order by teamname";
            }
            
            $query->setQuery($sql);
            $db->setQuery($query);
            
            // Load the results as a list of stdClass objects (see later for more options on retrieving data).
            $rows = $db->loadAssocList();
            
            return $rows;
        }
        

        public static function getTeamList2($programid, $divisionid) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            
           $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, d.agegroup, a.contactname FROM " .
                    $db->quoteName('#__jsports_teams') . " as a, " .
                    $db->quoteName('#__jsports_map') . " as m, " .
                    $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid in (
                    select divisionid from #__jsports_map as m, #__jsports_divisions as d
                    where m.divisionid = d.id
                    and m.divisionid = " . $divisionid . " and m.programid = " . $programid . "
                    )";
            
            $query->setQuery($sql);
            $db->setQuery($query);
            
            // Load the results as a list of stdClass objects (see later for more options on retrieving data).
            $rows = $db->loadAssocList();
            
            return $rows;
        }

        public static function getTeamsByAgeGroup($programid, $agegroup, $divisionid) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            
            $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, d.agegroup, a.contactname FROM " .
                $db->quoteName('#__jsports_teams') . " as a, " .
                $db->quoteName('#__jsports_map') . " as m, " .
                $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid in (
                    select divisionid from #__jsports_map as m, #__jsports_divisions as d
                    where m.divisionid = d.id
                    and d.agegroup = " . $agegroup . " and d.id <> " . $divisionid . " and m.programid = " . $programid . "
                    )
	 	order by teamname
		";     
                
                $query->setQuery($sql);
                $db->setQuery($query);
                
                // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                $rows = $db->loadAssocList();
                
                return $rows;
        }
    }
    
