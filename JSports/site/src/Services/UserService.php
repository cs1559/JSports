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

use FP4P\Component\JSports\Administrator\Table\GamesTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

class UserService
{
    /**
     * This is a helper function that returns the current user object from Joomla.
     *
     * @return unknown
     */
    public static function getUser() {
        return Factory::getUser();
        
    }

    /**
     * This function will return a list of teams a user is associated with - either as the owner or as a staff
     * roster entry where their user id was set.
     *
     * NOTE:  THERE IS A POTENTIAL ISSUE WHEN A TEAM PLAYS MULTIPLE YEARS AND A STAFF MEMBER WAS GIVEN PERMISSION BUT THEY WERE NO LONGER
     * WITH THE TEAM IN THEIR FINAL SEASON.  THAT USER MAY HAVE ACCESS TO EDIT THE TEAM INFORMATION.
     *
     * @param unknown $uid
     * @return array|unknown
     */
    public static function getUserTeams($uid = null) {
        
        if (is_null($uid)) {
            $user = Factory::getUser();
            $uid = $user->id;
            if ($user->guest) {
                return array();
                
            }
        }
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $sql = "select distinct t.*,  p.name as lastprogramname
            from #__jsports_teams t, #__jsports_map m, #__jsports_programs p,
            (select max(programid) xid from #__jsports_map
                group by teamid) as maxp
                where t.id = m.teamid
                and m.programid = p.id
                and m.programid = maxp.xid
                and t.ownerid = " . $db->quote($uid) . "
            UNION
            select distinct t.*, p.name as lastprogramname
            from #__jsports_teams t, #__jsports_map m, #__jsports_programs p,
            	(select max(programid) xid from #__jsports_map group by teamid) as maxp,
                #__jsports_rosters r
            where t.id = m.teamid and m.programid = p.id and m.programid = maxp.xid
            	and r.programid = m.programid
                and r.teamid = m.teamid
            	and r.userid = " . $db->quote($uid);
                    
            
        $db->setQuery($sql);
        return $db->loadObjectList();
        
    
    }
    
    /**
     * This function will return a list of TEAM ID's they are associated with.
     *
     * @param unknown $uid
     * @return array|NULL[]
     */
    public static function getUserTeamIds($uid = null) {
        
        if (is_null($uid)) {
            $user = Factory::getUser();
            $uid = $user->id;
            if ($user->guest) {
                return array();
                
            }
        }
        $retArray = array();
        $list = UserService::getUserTeams($uid);
        
        foreach ($list as $item) {
            $retArray[] = $item->teamid;
        }
        return $retArray;
    }
    
    
    /**
     * This function will determine if a user is an "admin" for a team during a given program.
     *
     * @param unknown $teamid
     * @param unknown $programid
     * @param unknown $uid
     * @return number
     */
    public static function isTeamAdmin($teamid, $programid, $uid) {
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query2 = $db->getQuery(true);
        
        $query->select($db->quoteName(array('id')));
        $query->from($db->quoteName('#__jsports_teams'));
        $conditions = array(
            $db->quoteName('ownerid') . ' = ' . $db->quote($uid),
            $db->quoteName('id') . ' = ' . $db->quote($teamid),
        );
        $query->where($conditions);
        
        $query2->select($db->quoteName(array('userid')));
        $query2->from($db->quoteName('#__jsports_rosters'));
        $conditions2 = array(
            $db->quoteName('userid') . ' = ' . $db->quote($uid),
            $db->quoteName('teamid') . ' = ' . $db->quote($teamid),
            $db->quoteName('programid') . ' = ' . $db->quote($programid),
        );
        $query2->where($conditions2);
        
        $query->union($query2);
        
        $db->setQuery($query);
        
        $rows = $db->loadObjectList();
        
        return count($rows);
    }
    
    
    /**
     * this function just indicates if the user is a guest or not.  the intention was to abstract this joomla specific logic to
     * the core code.
     *
     * @return unknown
     */
    public static function isGuest() {
        $user = Factory::getUser();
        return $user->guest;
    }
    
    /**
     *
     * SELECT d.agegroup
FROM xkrji_jsports_teams t, xkrji_jsports_map m, xkrji_jsports_divisions d
where t.id = m.teamid
and m.divisionid = d.id
and t.ownerid = 640
UNION
SELECT d.agegroup
FROM xkrji_jsports_rosters r, xkrji_jsports_map m, xkrji_jsports_divisions d
where r.teamid = m.teamid
and m.divisionid = d.id
and r.userid = 640;

     */

    /**
     * the getAssignedAgeGroups will return an array of age groups a given user has been assigned too either based
     * on the owner id of the team or as a result of a user id being assigned at the ROSTER level.
     *
     * @param unknown $uid
     * @return array
     */
    public static function getAssignedAgeGroups($uid = null) {
        
        if (is_null($uid)) {
            $user = Factory::getUser();
            $uid = $user->id;
            if ($user->guest) {
                return array();
              
            }
        }
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query2 = $db->getQuery(true);
        
        $query->select($db->quoteName(array('d.agegroup')));
        $query->from($db->quoteName('#__jsports_teams') . ' as t, '.
                $db->quoteName('#__jsports_map') . ' as m, '.
                $db->quoteName('#__jsports_divisions') . ' as d '
            );
        
        $conditions = array(
            $db->quoteName('t.id') . ' = ' . $db->quoteName('m.teamid'),
            $db->quoteName('m.divisionid') . ' = ' . $db->quoteName('d.id'),
            $db->quoteName('ownerid') . ' = ' . $db->quote($uid),
        );
        $query->where($conditions);
        
        $query2->select($db->quoteName(array('d.agegroup')));
        $query2->from($db->quoteName('#__jsports_rosters') . ' as r, '.
            $db->quoteName('#__jsports_map') . ' as m, '.
            $db->quoteName('#__jsports_divisions') . ' as d '
            );
        $conditions2 = array(
            $db->quoteName('r.teamid') . ' = ' . $db->quoteName('m.teamid'),
            $db->quoteName('m.divisionid') . ' = ' . $db->quoteName('d.id'),
            $db->quoteName('r.userid') . ' = ' . $db->quote($uid),
        );
        $query2->where($conditions2);
        $query->union($query2);
        
        
        $db->setQuery($query);
        
        return $db->loadObjectList();
        
    }
}

