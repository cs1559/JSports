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
 * 02/13/2024 - Added CONTACT NAME to the option value in the select element.
 * Also added OPTION GROUPS for cross-divisional play.
 */
namespace FP4P\Component\JSports\Site\Services;

use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\DivisionService;

class TeamService
{

    public static function getTeamsTable()
    {
        $db = Factory::getDbo();
        return new TeamsTable($db);
    }

    /**
     * This function will return an individual row based on the Team ID.
     *
     * @param number $id
     * @return FP4P\Component\JSports\Administrator\Table\TeamsTable|NULL
     */
    public static function getItem($id = 0)
    {
        $db = Factory::getDbo();
        $team = new TeamsTable($db);

        $row = $team->load($id);

        if ($row) {
            return $team;
        }

        return null;
    }

    public function updateTeamLogoFilename($teamid, $filename)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $fields = array(
            $db->quoteName('logo') . '= :filename_value'
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = :teamid'
        );

        $query->update($db->quoteName('#__jsports_teams'))
            ->set($fields)
            ->where($conditions);

        $query->bind(':teamid', $teamid, ParameterType::INTEGER)->bind(':filename_value', $filename, ParameterType::STRING);

        $db->setQuery($query);

        return $db->execute();
    }

    public static function getMostRecentProgram($teamid)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        // $query->select($db->quoteName(array('lastplayed', 'lastprogramid')));
        // $query->from($db->quoteName('#__jsports_view_lastplayed'));
        // $query->where($db->quoteName('teamid') . ' = :id' );
        // $query->bind(':id', $teamid, ParameterType::INTEGER);

        // $query->select($db->quoteName(array('max(programid)')));
        // $query->from($db->quoteName('#__jsports_map'));
        // $query->where($db->quoteName('teamid') . ' = :id' );
        // $query->bind(':id', $teamid, ParameterType::INTEGER);

        $query = "SELECT max(programid) as lastprogramid FROM `#__jsports_map` WHERE teamid = " . $teamid;

        $db->setQuery($query);

        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return $db->loadAssoc();
    }

    public static function getTeamDivisionId($teamid, $programid)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array(
            'teamid',
            'programid',
            'divisionid'
        )));
        $query->from($db->quoteName('#__jsports_map'));

        $conditions = array(
            $db->quoteName('teamid') . ' = ' . $db->quote($teamid),
            $db->quoteName('programid') . ' = ' . $db->quote($programid)
        );

        $query->where($conditions);
        $db->setQuery($query);

        $row = $db->loadAssoc();

        return $row['divisionid'];
    }

    public static function getTeamList($teamid, $programid, $divisionid = null)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        if (is_null($divisionid)) {
            $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, 
                            d.agegroup, a.contactname FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
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
        return $db->loadAssocList();
    }

    public static function getTeamList2($programid, $divisionid)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, 
                        d.agegroup, a.contactname FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
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
        return $db->loadAssocList();
    }

    public static function getTeamsByProgram($programid)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
//         $sql = "SELECT a.id as teamid, a.name as teamname, a.contactname, a.contactphone,
        $sql = "SELECT a.id as teamid, a.name as teamname, a.contactname, a.contactphone,
a.contactemail, a.city, a.state, m.divisionid, d.name as divisionname,
                        d.agegroup, a.contactname FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.programid = " . $programid . "
                and m.published = 1 
                ";
        
        $query->setQuery($sql);
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return $db->loadAssocList();
    }
    
    public static function getTeamsByAgeGroup($programid, $agegroup, $divisionid)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, 
                    d.name as divisionname, d.agegroup, a.contactname FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
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
        return $db->loadAssocList();
    }
    
    public static function getTeamEmailAddresses($teamid)
    {
                
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $sql = "
select distinct email from (
    select email from #__users as u
    where id in (select ownerid from #__jsports_teams where id = '" . $teamid . "')
    UNION
    select email from #__users as u
    where id in (select userid from #__jsports_rosters where teamid = '" . $teamid . "'
             and classification = 'S'
             and userid > 0)
) as temp";
        
        $query->setQuery($sql);
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        $emails = array();
        foreach ($rows as $row) {
            /* Eliminate any field thaat may have multiple @ signs */
            if (substr_count($row->email, '@')<=1) {
                $emails[] = $row->email;
            }
        }

        return $emails;
       
    }
    
    /**
     * This function updates the team profile HIT counter (# of views).
     * 
     * @param int $teamid
     */
    public static function hit($teamid) {
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $conditions = array($db->quoteName('id') . ' = ' .$teamid);
        
        $query->update($db->quoteName('#__jsports_teams'))
            ->set($db->quoteName('hits') . ' = ' . $db->quoteName('hits') . ' + 1 ')
	        ->where($conditions);
	        $db->setQuery($query);
	        $db->execute();
        
    }
    
    /**
     * This function determines if the team is an ACTIVE team within the league.  ACTIVE status
     * is determined if their team ID is are defined in an ACTIVE program.
     * 
     * @param int $teamid
     * @return int (0 = false, >1 = true)
     */
    public static function isActive($teamid) {
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $sql = "
            SELECT * FROM `#__jsports_teams` 
            WHERE id in (
                select teamid from #__jsports_map m, #__jsports_programs p 
                where m.programid = p.id and teamid = " . $teamid . " and p.status = 'A' 
                    and m.published = 1
        )";
        
        $query->setQuery($sql);
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return count($rows);
        
    }
    
}
    
