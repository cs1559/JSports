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
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\LogService;

class TeamService
{

    public static function getTeamsTable() : TeamsTable
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        return new TeamsTable($db);
    }

    /**
     * This function will return an individual row based on the Team ID.
     *
     * @param number $id
     * @return TeamsTable|NULL
     */
    public static function getItem($id = 0) : ?TeamsTable
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $team = new TeamsTable($db);

        $row = $team->load($id);

        if ($row) {
            return $team;
        }
        return null;
    }

    /**
     * This function will update the filename used for the team logo.
     * 
     * @todo  this function needs to be changed to a static function
     * @param number $teamid
     * @param string $filename
     * @return boolean
     */
    public function updateTeamLogoFilename(int $teamid, string $filename) : bool
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
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

        try {
            $db->execute();
            return true;
        } catch (\RuntimeException $e) {
            LogService::error("Error updating Team Logo Filename (" . $teamid . ") - " . $e->getMessage());
            return false;
        }
        
    }

    /**
     * This function will return the most recent program/season for a given team.
     * 
     * @param number $teamid
     * @return array<string, mixed> -- A single query row as an associated array
     */
    public static function getMostRecentProgram(int $teamid) : array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
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

        $sql = "SELECT max(programid) as lastprogramid FROM `#__jsports_map` WHERE teamid = :id";
        $query->setQuery($sql);
        $query->bind(':id', $teamid, ParameterType::INTEGER);

        $db->setQuery($query);
        return $db->loadAssoc();
    }
    
    /**
     * This function will return the most recent program/season ID for a given team.
     *
     * @param number $teamid
     * @return number | null
     */
    public static function getMostRecentProgramId(int $teamid) : ?int
    {
        $programid = 0;
        
        //         $db = Factory::getDbo();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
                
        $sql = "SELECT max(programid) as lastprogramid FROM `#__jsports_map` WHERE teamid = :id";
        $query->setQuery($sql);
        $query->bind(':id', $teamid, ParameterType::INTEGER);
        
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $result = $db->loadAssoc(); // should return only one row
        if (isset($result['lastprogramid'])) {
            $programid = $result['lastprogramid'];
        } else {
            $programid = null;
        }
        
        return $programid;
    }

    /**
     * This function will return the division id for a given team/program.
     * 
     * @param number $teamid
     * @param number $programid
     * @return number | null
     */
    public static function getTeamDivisionId(int $teamid, int $programid) : ?int
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array(
            'teamid',
            'programid',
            'divisionid'
        )));
        $query->from($db->quoteName('#__jsports_map'));

        $conditions = array(
            $db->quoteName('teamid') . ' = :teamid ' ,
            $db->quoteName('programid') . ' = :programid' 
        );
        
        $query->where($conditions);
        $query->bind(':teamid',$teamid, ParameterType::INTEGER);
        $query->bind(':programid',$programid, ParameterType::INTEGER);
        $db->setQuery($query);

        $row = $db->loadAssoc();   // should return only one row

        if (isset($row['divisionid'])) {
            return $row['divisionid'];
        } else {
            return null;
        }        
    }

    /**
     * This function will return a list of teams.  The return value is an array similar to:
     * 
     * [
        'id'   => '42',
        'name' => 'Bluff City Sox',
        'city' => 'Alton'
    ],
     * 
     * @todo  Need to revisit this.  seems like the sql isn't complete if a division id is provided - would cause error
     * @param number $teamid
     * @param number $programid
     * @param number $divisionid
     * @return array<int, array<string, mixed>>
     */
    public static function getTeamList(int $teamid, int $programid, ?int $divisionid = null)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        if (is_null($divisionid)) {
//             $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname,
//                             d.agegroup, a.contactname FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
//                 where m.teamid = a.id
//                 and m.divisionid = d.id
//                 and m.divisionid in (
//                     select divisionid from " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
//                     where m.divisionid = d.id
//                     and m.teamid = " . $db->quote($teamid) . " and m.programid = " . $db->quote($programid) . "
//                     )
// 		          order by teamname";
            $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname,
                        d.agegroup, a.contactname, a.contactphone, a.contactemail, a.city, a.state
                FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid in (
                    select divisionid from " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                    where m.divisionid = d.id
                    and m.teamid = :teamid and m.programid = :programid
                    )
		          order by teamname";
        } else {
            $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname,
                        d.agegroup, a.contactname, a.contactphone, a.contactemail, a.city, a.state
                FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid = :divisionid
                and m.divisionid in (
                    select divisionid from " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                    where m.divisionid = d.id
                    and m.teamid = :teamid and m.programid = :programid
                    )
		          order by teamname";
        }

        $query->setQuery($sql);
        $query->bind(':teamid', $teamid, ParameterType::INTEGER)
            ->bind(':programid', $programid, ParameterType::INTEGER);
        if (!is_null($divisionid)) {
            $query->bind(':divisionid', $divisionid, ParameterType::INTEGER);
        }
        $db->setQuery($query);

        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return $db->loadAssocList();
    }

    /**
     * This function will return an array of teams (limited attributes) within a given division.
     * @deprecated
     * @param number $programid
     * @param number $divisionid
     * @return array<int, array<string, mixed>>
     */
    public static function getTeamList2($programid, $divisionid)
    {
        return self::getTeamsByDivision($programid, $divisionid);
//         $db = Factory::getDbo();
//         $db = Factory::getContainer()->get(DatabaseInterface::class);
//         $query = $db->getQuery(true);

//         $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, 
//                         d.agegroup, a.contactname FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
//                 where m.teamid = a.id
//                 and m.divisionid = d.id
//                 and m.divisionid in (
//                     select divisionid from #__jsports_map as m, #__jsports_divisions as d
//                     where m.divisionid = d.id
//                     and m.divisionid = " . $divisionid . " and m.programid = " . $programid . "
//                     )";

//         $query->setQuery($sql);
//         $db->setQuery($query);

//         // Load the results as a list of stdClass objects (see later for more options on retrieving data).
//         return $db->loadAssocList();
    }

    /**
     * This function will return an array of teams (limited attributes) within a given division.
     *
     * @param number $programid
     * @param number $divisionid
     * @return array<int, array<string, mixed>>
     */
    public static function getTeamsByDivision(int $programid, int $divisionid) : array
    {
        //         $db = Factory::getDbo();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
           
        $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname,
                        d.agegroup, a.contactname, a.contactphone, a.contactemail, a.city, a.state
                FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid in (
                    select divisionid from #__jsports_map as m, #__jsports_divisions as d
                    where m.divisionid = d.id
                    and m.divisionid = :divisionid and m.programid = :programid
                    )";
        
        $query->setQuery($sql);
        $query->bind(':divisionid', $divisionid, ParameterType::INTEGER)
            ->bind(':programid', $programid, ParameterType::INTEGER);
        
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    /**
     * This function returns an array of teams by a given program.
     * 
     * @param number $programid
     * @return array<int, array<string, mixed>>
     */
    public static function getTeamsByProgram(int $programid) : array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);     
        $query = $db->getQuery(true);
        
        $sql = "SELECT a.id as teamid, a.name as teamname, a.contactname, a.contactphone,
a.contactemail, a.city, a.state, m.divisionid, d.name as divisionname, d.agegroup 
                FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.programid = :programid
                and m.published = 1 
                ";
        
        $query->setQuery($sql);
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    /**
     * 
     * @param int $programid
     * @param int $agegroup
     * @param int $divisionid
     * @return array<int, array<string, mixed>>
     */
    public static function getTeamsByAgeGroup(int $programid, int $agegroup, int $divisionid) : array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, 
                    d.name as divisionname, d.agegroup, a.contactname FROM " . $db->quoteName('#__jsports_teams') . " as a, " . $db->quoteName('#__jsports_map') . " as m, " . $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid in (
                    select divisionid from #__jsports_map as m, #__jsports_divisions as d
                    where m.divisionid = d.id
                    and d.agegroup = :agegroup and d.id <> :divisionid and m.programid = :programid
                    )
	 	order by teamname
		";

        $query->setQuery($sql);
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        $query->bind(':divisionid', $divisionid, ParameterType::INTEGER);
        $query->bind(':agegroup', $agegroup, ParameterType::INTEGER);
        
        $db->setQuery($query);

        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return $db->loadAssocList();
    }
    
    /**
     * 
     * @param int $teamid
     * @return array
     */
    public static function getTeamEmailAddresses(int $teamid) : array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $sql = "
select distinct email from (
    select email from #__users as u
    where id in (select ownerid from #__jsports_teams where id = :teamid1 )
    UNION
    select email from #__users as u
    where id in (select userid from #__jsports_rosters where teamid = :teamid2
             and classification = 'S'
             and userid > 0)
) as temp";
        
        $query->setQuery($sql);
        $query->bind(':teamid1', $teamid, ParameterType::INTEGER);
        $query->bind(':teamid2', $teamid, ParameterType::INTEGER);
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
    public static function hit($teamid) : void {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
        ->update($db->quoteName('#__jsports_teams'))
        ->set($db->quoteName('hits') . ' = ' . $db->quoteName('hits') . ' + 1')
        ->where($db->quoteName('id') . ' = :teamid')
        ->bind(':teamid', $teamid, ParameterType::INTEGER);
        
        $db->setQuery($query)->execute();
    }
    
    /**
     * This function determines if the team is an ACTIVE team within the league.  ACTIVE status
     * is determined if their team ID is d.efined in an ACTIVE program.  An active program is identified
     * by the "status" column
     * 
     * @param number $teamid
     * @return boolean
     */
        public static function isActive(int $teamid) : bool {
    
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            
            $query = $db->getQuery(true)
            ->select('1')
            ->from($db->quoteName('#__jsports_map', 'm'))
            ->innerJoin($db->quoteName('#__jsports_programs', 'p') . ' ON p.id = m.programid')
            ->where('m.teamid = :teamid')
            ->where('m.published = 1')
            ->where('p.status = :status')
            ->bind(':teamid', $teamid, ParameterType::INTEGER)
            ->bind(':status', 'A', ParameterType::STRING);
            
            $db->setQuery($query, 0, 1);
            
            return (bool) $db->loadResult();
            
    //         $db = Factory::getDbo();
    //         $db = Factory::getContainer()->get(DatabaseInterface::class);
    //         $query = $db->getQuery(true);
            
    //         $sql = "
    //             SELECT * FROM `#__jsports_teams` 
    //             WHERE id in (
    //                 select teamid from #__jsports_map m, #__jsports_programs p 
    //                 where m.programid = p.id and teamid = " . $teamid . " and p.status = 'A' 
    //                     and m.published = 1
    //         )";
            
    //         $query->setQuery($sql);
    //         $db->setQuery($query);
    //         $rows = $db->loadObjectList();
    //         return count($rows);
            
        }
    
}
    
