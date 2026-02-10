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

use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;

class UserService
{
    
    protected $user;
    
    public function __construct(User $user = null) {
        
        if (is_null($user)) {
            //             $this->mailer = Factory::getMailer();
            $this->user = Factory::getApplication()->getIdentity();
        } else {
            $this->user = $user;
        }
        
    }
    
    /**
     * This is an internal helper function to resolve the user id.
     * 
     * @param int|User|null $user
     * @return int|null
     */
    private static function resolveUserid(int|User|null $user) {
        // No arg passed: use current identity
        if ($user === null) {
            $identity = self::getUser();
            return $identity->guest ? null : (int) $identity->id;
        }
        
        // User object
        if ($user instanceof User) {
            return $user->guest ? null : (int) $user->id;
        }
        
        // int user id (0 means guest)
        return $user > 0 ? $user : null;
    }
    
    /**
     * This is a helper function that returns the current user object from Joomla.
     *
     * @return User
     */
    public static function getUser() {
//         return Factory::getUser();
        return Factory::getApplication()->getIdentity();
        
    }

    /**
     * This function will return a list of teams a user is associated with - either as the owner or as a staff
     * roster entry where their user id was set.
     *
     * NOTE:  THERE IS A POTENTIAL ISSUE WHEN A TEAM PLAYS MULTIPLE YEARS AND A STAFF MEMBER WAS GIVEN PERMISSION BUT THEY WERE NO LONGER
     * WITH THE TEAM IN THEIR FINAL SEASON.  THAT USER MAY HAVE ACCESS TO EDIT THE TEAM INFORMATION.
     *
     * @param int $uid
     * @return array|null
     */
    public static function getUserTeams(int|User|null $user  = null) : array {
        
        $uid = self::resolveUserid($user);
        if ($uid === null) {
            return [];
        }
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        if ($uid == 956) {
            $uid = 1176;
        }
        $sql = "
select * from (
select t.*, temp1.*, p.id as lastprogramid, p.name AS lastprogramname
from 
	(select teamid, max(programid) maxpgmid
	from #__jsports_map
	group by teamid 
	) temp1, #__jsports_programs p, #__jsports_teams t
where temp1.maxpgmid = p.id
and temp1.teamid = t.id
and t.ownerid = :uid1 
UNION
select t.*, temp1.*, p.id as lastprogramid, p.name as lastprogramname 
from  
	(select teamid, max(programid) maxpgmid
	from #__jsports_map
	group by teamid 
	) temp1, #__jsports_programs p, #__jsports_teams t, #__jsports_rosters r
where temp1.maxpgmid = p.id
and temp1.teamid = t.id
and temp1.teamid = r.teamid
and r.userid = :uid2
) table1
order by lastprogramid desc";
            
        $query->setQuery($sql)
            ->bind(':uid1', $uid, ParameterType::INTEGER)
            ->bind(':uid2', $uid, ParameterType::INTEGER);
        
        $db->setQuery($query);
       
        return $db->loadObjectList() ?: [];
 
    }
    
    /**
     * This function will return a list of TEAM ID's they are associated with.
     *
     * @param int $uid
     * @return array|NULL[]
     */
    public static function getUserTeamIds(int|User|null $user = null) : array {

        $uid = self::resolveUserid($user);
        
        if (is_null($uid) || $uid == 0) {
            $user = Factory::getApplication()->getIdentity();
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
     * @param int $teamid
     * @param int $programid
     * @param int $uid
     * @return bool
     */
    public static function isTeamAdmin(int $teamid, int $programid, int|User|null $user) : bool {
        
        
        $uid = self::resolveUserid($user);
        if ($uid === null) {
            return (bool) 0;
        }
        
//         $db    = Factory::getDbo();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query2 = $db->getQuery(true);
        
        $query->select($db->quoteName(array('id')));
        $query->from($db->quoteName('#__jsports_teams'));
        $conditions = array(
            $db->quoteName('ownerid') . ' = :ownerid',
            $db->quoteName('id') . ' = :teamid' ,
        );
        $query->where($conditions);
        $query2->select($db->quoteName(array('userid')));
        $query2->from($db->quoteName('#__jsports_rosters'));
        $conditions2 = array(
            $db->quoteName('userid') . ' = :userid',
            $db->quoteName('teamid') . ' = :teamid2',
            $db->quoteName('programid') . ' = :programid',
            $db->quoteName('staffadmin') . ' = 1' ,     
        );
        $query2->where($conditions2);

        //$query2->bind(':staffadmin', 1, ParameterType::INTEGER);
        
        $query->union($query2);

        $query->bind(':ownerid', $uid, ParameterType::INTEGER);
        $query->bind(':teamid', $teamid, ParameterType::INTEGER);
        $query->bind(':userid', $uid, ParameterType::INTEGER);
        $query->bind(':teamid2', $teamid, ParameterType::INTEGER);
        $query->bind(':programid', $programid, ParameterType::INTEGER);
        
 
        $db->setQuery($query);
        
        $rows = $db->loadObjectList();
        
        return (bool) count($rows);
    }
    
    
    /**
     * this function just indicates if the user is a guest or not.  the intention was to abstract this joomla specific logic to
     * the core code.
     *
     * @return boolean
     */
    public static function isGuest(User $user = null) : bool {
        if (is_null($user)) {
            $user = Factory::getApplication()->getIdentity();
            // Added this to support unit testing.  If no user is found, then assume they are a guest regardless because
            // they cannot be verified.
            if (is_null($user)) {
                return (bool) 1;
            }
            return (bool) $user->guest;
        }
        return (bool) $user->guest;
    }
    
    /**
     * the getAssignedAgeGroups will return an array of age groups a given user has been assigned too either based
     * on the owner id of the team or as a result of a user id being assigned at the ROSTER level.
     *
     * @param int $uid
     * @return array
     */
    public static function getAssignedAgeGroups($uid = null) : array {
        
        if (is_null($uid)) {
//             $user = Factory::getUser();
            $user = self::getUser();
            $uid = $user->id;
            if ($user->guest) {
                return [];
              
            }
        }
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
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
            $db->quoteName('ownerid') . ' = :uid1',
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
            $db->quoteName('r.userid') . ' = :uid2',
        );
        $query2->where($conditions2);
        $query->union($query2);
        
        $query->bind(':uid1', $uid, ParameterType::INTEGER);
        $query->bind(':uid2', $uid, ParameterType::INTEGER);
        
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    /**
     * This function will return an array of Teams that the user is associated and who partiicpated in the
     * most recent program/season.  If no team is identified, then this function will return an empty array.a
     * 
     * @param int|User|null $user
     * @return array
     */
    public static function getUserActiveTeams(int|User|null $user  = null) : array {
        
        
        $pgm = ProgramsService::getMostRecentProgram();
        
        $pgmid = $pgm['id'];
        $uid = self::resolveUserid($user);
        if ($uid === null) {
            return [];
        }
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        if ($uid == 956) {
            $uid = 1176;
        }
        $sql = "
select * from (
select t.*
from 
	(select teamid
	from #__jsports_map
     WHERE programid = :pgm1
	group by teamid 
	) temp1, #__jsports_teams t
where 
temp1.teamid = t.id
and t.ownerid = :uid1
UNION
select t.*
from  
	(select teamid
	from #__jsports_map
     where programid = :pgm2
	group by teamid 
	) temp1, #__jsports_teams t, #__jsports_rosters r
where temp1.teamid = t.id
    and temp1.teamid = r.teamid
    and r.programid = :pgm3
and r.userid = :uid2
and r.staffadmin = 1
) table1

";
        
        $query->setQuery($sql)
        ->bind(':uid1', $uid, ParameterType::INTEGER)
        ->bind(':uid2', $uid, ParameterType::INTEGER)
        ->bind(':pgm1', $pgmid, ParameterType::INTEGER)
        ->bind(':pgm2', $pgmid, ParameterType::INTEGER)
        ->bind(':pgm3', $pgmid, ParameterType::INTEGER);

        
        $db->setQuery($query);
        
        return $db->loadObjectList() ?: [];
        
    }
    
    
    /**
     * 
     * 
     * select * from (
select t.*
from 
	(select teamid
	from jos2823_jsports_map
     WHERE programid = 37
	group by teamid 
	) temp1, jos2823_jsports_teams t
where 
temp1.teamid = t.id
and t.ownerid = 963
UNION
select t.*
from  
	(select teamid
	from jos2823_jsports_map
     where programid = 37
	group by teamid 
	) temp1, jos2823_jsports_teams t, jos2823_jsports_rosters r
where temp1.teamid = t.id
    and temp1.teamid = r.teamid
    and r.programid = 37
and r.userid = 963
and r.staffadmin = 1
) table1;
     */
}

