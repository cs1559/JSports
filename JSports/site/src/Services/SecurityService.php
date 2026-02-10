<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Services;


use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\DivisionService;
use Joomla\CMS\User\User;
use FP4P\Component\JSports\Administrator\Table\GamesTable;

class SecurityService
{
    
    /**
     * This function returns a Joomla User Object
     * 
     * @return User
     */
    public static function getUser() : User {
//         $user = Factory::getApplication()->getIdentity();
        $user = UserService::getUser();
        return $user;
    }
    
    /**
     * Function that determines if a sepcific user can EDIT a team.  the context MUST contain a teamid.
     *
     * @param array
     * @return boolean
     */
    public static function canEditTeam(array $context) : bool {
        
        if (isset($context['teamid'])) {
            $teamid = (int) ($context['teamid'] ?? 0);
            if ($teamid <= 0) {
                return false;
            }
        } else {
            return false;
        }
        $ownerid = isset($context['ownerid']) ? (int) $context['ownerid'] : null;
        $programid = isset($context['programid']) ? (int) $context['programid'] : TeamService::getMostRecentProgram($teamid);
//         if (isset($context['programid'])) {
//             $programid = $context['programid'];
//         } else {
//             $programid = TeamService::getMostRecentProgram($teamid);
//         }
        
        // Check to see if FRONT END ADMINISTRATIN is enabled.  Prevent all
        // users from editing items on the front end if disabled. League
        // Administrators can still log into the backend and make necessary changes.
        $params = ComponentHelper::getParams('com_jsports');
        $frontendenabled = $params->get('frontend_admin');
   
        
        // If front-end adminstration is turned off, return FASLE;
        if (!$frontendenabled) {
            return false;
        }
        
        // Get current user
        //$user = Factory::getUser();
        $user = SecurityService::getUser();
        
        // If the user is a GUEST, return FALSE
        if ($user->guest) {
            return false;
        }
        // If the user is a "super user" or "administrator" immediately grant them access
        if (SecurityService::isAdmin()) {
            return true;
        }
        
        if (is_null($ownerid)) {
            $svc = new TeamService();
            $team = $svc->getItem($teamid);
            if (!$team) {
                return false;
            }
            if ($team->id == 0) {
                return false;
            }
            
            if ((int) $team->ownerid === (int) $user->id) {
                return true;
            }
            return false;
        }
        
        if ((int) $ownerid === (int) $user->id) {
            return true;
        }
        
        // Check to see if user is listed on the Staff Rosters of a team.

        //$pgm = ProgramsService::getMostRecentProgram();
        if (UserService::isTeamAdmin($teamid, $programid, $user->id)){
            return true;
        }
        return false;
        
    }
    
    /**
     * This function verifies that a specific user can EDIT a team's roster.  Additional
     * business rules can be added here. Current business rules prohibit a team roster
     * from being edited if the rosters are "locked" (set at the program level) and
     * if the program/season is currently CLOSED.  
     * 
     * These rules should prohibit a coach who had been previously tied to a team from editing
     * that same team's roster in another season in case they were removed from thier staff
     * roster.
     *
     * @param number $teamid
     * @param number $programid
     * @param number $ownerid
     * @return boolean
     */
    public static function canEditTeamRoster(int $teamid, int $programid, ?int $ownerid = null) : bool {
        
        // If the user is a "super user" or "administrator" immediately grant them access
        if (SecurityService::isAdmin()) {
            return true;
        }

        if (is_null($ownerid)) {
            //$user = Factory::getUser();
            $user = SecurityService::getUser();
            $ownerid = $user->id;
        }
        
        $context = array(
            'teamid' => $teamid,
            'ownerid' => $ownerid
        );
        $canEdit = SecurityService::canEditTeam($context);

        $pgm = ProgramsService::getItem($programid);
        
        if ($pgm->rosterslocked) {
            return false;
        }
        
        // Rosters for closed programs cannot be edited - Front End business rule.
        // No closed programs can have rosters edited.  Admins are required to change from the backend.
        if ($pgm->status == 'C') {
            return false;
        }
        
        return $canEdit;
    }
    
    /**
     * This function verifies that a specific user can EDIT a team's schedule.
     * Additional business rules can be added here.
     *

     * @param number $teamid
     * @param number $programid
     * @param number $ownerid
     * @return boolean
     */
    public static function canEditTeamSchedule(int $teamid, int $programid, ?int $ownerid = null) : bool {

        // If the user is a "super user" or "administrator" immediately grant them access
        if (SecurityService::isAdmin()) {
            return true;
        }
            
        $divisionid = TeamService::getTeamDivisionId($teamid, $programid);
        $division = DivisionService::getItem($divisionid);
        if (!$division) {
            return false;
        }
        
        // If the league manages the schedule for a divsion, then return false
        // regardless if the user owns the team profile.
        if ($division->leaguemanaged) {
            return false;
        }
        
        if (is_null($ownerid)) {
            //$user = Factory::getUser();
            $user = SecurityService::getUser();
            $ownerid = $user->id;
        }
        
        $pgm = ProgramsService::getItem($programid);
        if ($pgm->status == "C") {
            return false;
        }
        $context = array(
            'teamid' => $teamid,
            'ownerid' => $ownerid
        );
        return SecurityService::canEditTeam($context);
        
    }
    
    /**
     *
     * @param number $teamid
     * @param GamesTable $item         
     */
    public static function canEditGame(int $teamid, \stdClass $item ) : bool {
      //  $params = ComponentHelper::getParams('com_jsports');
      //  $editawaygame = $params->get('editawaygame');
        
        // Get current user
        //$user = Factory::getUser();
        $user = SecurityService::getUser();
        
        // If the user is a GUEST, return FALSE
        if ($user->guest) {
            return false;
        }
        
        // If the user is a "super user" or "administrator" immediately grant them access
        if (SecurityService::isAdmin()) {
            return true;
        }
        
        $teamAdmin = UserService::isTeamAdmin($teamid, $item->programid, $user->id);
        if (!$teamAdmin) {
            return false;
        }
        
        // IF THE COMPONENT IS CONFIGURED TO NOT ALLOW USERS TO EDIT AWAY GAMES
//         if (!$editawaygame) {
            
            if ($teamid != $item->hometeamid) {
                return false;
            }
            
//         }
        
        if ($item->gamestatus === 'C') {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * This function will return a boolean if the frontend administration is
     * Enabled or Disable.  If disabled, an authorized user will not be
     * presented a menu of functions for managing a team profile.
     *
     * @return boolean
     */
    public static function frontEndAdminEnabled() : bool {
        $params = ComponentHelper::getParams('com_jsports');
        return (bool) $params->get('frontend_admin');
    }

    
    /**
     * This function returns a boolean if the user has been granted ADMIN permissions
     * within the component.  This is beyond any type of admin role that was granted
     * to an individual via the Team Profile owner id attribute or via the staff roster.
     *
     * @return boolean
     */
    public static function isAdmin() : bool {
        // Get current user
        //$user = Factory::getUser();
        $user = SecurityService::getUser();
        
        // Chekcs to  see if the user is in an ADMINISTRATOR ROLE.
        if ($user->authorise('core.jsports.admin','com_jsports')) {
            return true;
        }

        if ($user->authorise('core.admin','com_jsports')) {
            return true;
        }
        // If the user is assigned "ownership" of a profile, then YES.
        //$teams = UserService::getUserteams($user->id);
//         if (count($teams)>0) {
//             return true;
//         }
        
        return false;
    }
    
    
    /**
     * This function returns a boolean if the specific user is a "coach".  Coach, in this context, is 
     * any user who is assigned as owner of a team or part of their staff.
     * @return boolean
     */
    public static function isCoach() : bool {
        
        //$user = Factory::getUser();
        $user = SecurityService::getUser();
        
        // If the current user a guest, return false
        if ($user->guest) {
            return false;
        }
        
        // If the user is assigned with ADMINISTRATOR permissions, then yes.
        if (SecurityService::isAdmin()) {
            return true;
        }
        // If the user is assigned "ownership" of a profile, then YES.
        $teams = UserService::getUserteams($user->id);
        if (count($teams)>0) {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * This function will check to see if a specific user can view another teams roster.  the general
     * rule is that they CANNOT unless they are an admin or within the same age group.
     * 
     * @param number $teamid
     * @param number $divisionid
     * @return boolean
     */
    public static function canViewTeamRoster(int $teamid, int $divisionid = 0) : bool {
        
        $retval= false;
        
        //$user = Factory::getUser();
        $user = SecurityService::getUser();
        
        if ($user->guest) {
            return false;
        }
        
        // 2024-03-11 - added to allow all league admins to view roster.
        if (SecurityService::isAdmin()) {
            return true;
        }
        
        $division = DivisionService::getItem($divisionid);
        if (!$division) {
            return false;
        }
        $allowed_agegroups = UserService::getAssignedAgeGroups();
        
        foreach ($allowed_agegroups as $item) {
            if ($item->agegroup == $division->agegroup) {
                $retval = true;
            }
        }
        
        return $retval;
    }
    
    
    /**
     * This function determines if a user can edit a specific team bulletin.
     * 
     * @param int $teamid
     * @return boolean
     */
    public static function canEditTeamBulletins(int $teamid) : bool {
        $retval = false;
        //$user = Factory::getUser();
        $user = SecurityService::getUser();
        
        // If the current user a guest, return false
        if ($user->guest) {
            return false;
        }
        
        // If the user is assigned with ADMINISTRATOR permissions, then yes.
        if (SecurityService::isAdmin()) {
            return true;
        }
        $teamids = UserService::getUserTeamIds($user->id);
        if (in_array($teamid, $teamids, true)) {
            return true;
        }
        
        return $retval;
    }
    
    public static function canManageBulletins() : bool {

        $user = SecurityService::getUser();
        
        // If the current user a guest, return false
        if ($user->guest) {
            return false;
        }
        
        // If the user is assigned with ADMINISTRATOR permissions, then yes.
        if (SecurityService::isAdmin()) {
            return true;
        }
        
        $teams = UserService::getUserActiveTeams();
        if ($teams) {
            return true;
        }
        
        if ($user->authorise('core.jsports.bulletins.manage', 'com_jsports'))
        {
            return true;
        }
        
//         $params = ComponentHelper::getParams('com_jsports');
//         $authorizedgroup = $params->get('groupid');
        
//         // If user is in a specific group -- MAY NOT BE NEEDED
//         if (in_array($authorizedgroup, $user->getAuthorisedGroups()))
//         {
//             return true;
//         }
        
        return false;
    }
    
}


