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

/**
 * CHANGE HISTORY:
 * - Fixed isAdmin function.  There was logic to check if a person was
 *    assigned to a team and that wasn't the intention of that function.
 * 2024-03-07 - Added logic to set the ownerid parameter to user-> on
 *              canEditTeamRoster and canEditTeamSchedule
 * 2024-03-11 - Changed code to check isAdmin when the viewTeamRoster
 *              function is called.
 *
 */
namespace FP4P\Component\JSports\Administrator\Services;


use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\DivisionService;

class SecurityService
{
    
    /**
     * Function that determines if a sepcific user can EDIT a team.
     *
     * @param unknown $teamid
     * @param unknown $ownerid
     * @return boolean
     */
    public static function canEditTeam(array $context) {
        
        if (isset($context['teamid'])) {
            $teamid = $context['teamid'];
        } else {
            return false;
        }
        
        if (isset($context['ownerid'])) {
            $ownerid = $context['ownerid'];
        } else {
            $ownerid = null;
        }
        if (isset($context['programid'])) {
            $programid = $context['programid'];
        } else {
            $programid = TeamService::getMostRecentProgram($teamid);
        }
        
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
        $user = Factory::getUser();
        
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
            
            if ($team->id == 0) {
                return false;
            }
            
            if ($team->ownerid === $user->id) {
                return true;
            }
            return false;
        }
        
        if ($ownerid === $user->id) {
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
     * @param int $teamid
     * @param int $programid
     * @param int $ownerid
     * @return boolean|unknown
     */
    public static function canEditTeamRoster($teamid, $programid, $ownerid = null) {
        
        // If the user is a "super user" or "administrator" immediately grant them access
        if (SecurityService::isAdmin()) {
            return true;
        }

        if (is_null($ownerid)) {
            $user = Factory::getUser();
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
     * @param int $teamid
     * @param int $programid
     * @param int $ownerid
     * @return boolean|unknown
     */
    public static function canEditTeamSchedule($teamid, $programid, $ownerid = null) {

        // If the user is a "super user" or "administrator" immediately grant them access
        if (SecurityService::isAdmin()) {
            return true;
        }
        
        if (is_null($ownerid)) {
            $user = Factory::getUser();
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
        
//        return SecurityService::canEditTeam(array('teamid' => $teamid, 'ownerid' => $ownerid));
        
        //return SecurityService::canEditTeam($teamid, $ownerid);
        
    }
    
    /**
     *
     * @param unknown $teamid
     * @param unknown $item         // Item from the SchedulesModel.
     */
    public static function canEditGame($teamid, Object $item ){
        $canEdit = true;
        
        if ($teamid != $item->teamid) {
            return false;
        }
        if ($item->gamestatus === 'C') {
            return false;
        }
        
        return $canEdit;
    }
    
    
    /**
     * This function will return a boolean if the frontend administration is
     * Enabled or Disable.  If disabled, an authorized user will not be
     * presented a menu of functions for managing a team profile.
     *
     * @return boolean
     */
    public static function frontEndAdminEnabled() {
        $params = ComponentHelper::getParams('com_jsports');
        return $params->get('frontend_admin');
        
    }

    
    /**
     * This function returns a boolean if the user has been granted ADMIN permissions
     * within the component.  This is beyond any type of admin role that was granted
     * to an individual via the Team Profile owner id attribute or via the staff roster.
     *
     * @return boolean
     */
    public static function isAdmin(){
        // Get current user
        $user = Factory::getUser();
        
        // Chekcs to  see if the user is in an ADMINISTRATOR ROLE.
        if ($user->authorise('core.jsports.admin','com_jsports')) {
            return true;
        }
        
        // If the user is assigned "ownership" of a profile, then YES.
        //$teams = UserService::getUserteams($user->id);
//         if (count($teams)>0) {
//             return true;
//         }
        
        return false;
    }
    
    
    public static function isCoach() {
        
        $user = Factory::getUser();
        
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
    
    
    
    public static function canViewTeamRoster($teamid, $divisionid = 0) {
        
        $retval= false;
        
        // 2024-03-11 - added to allow all league admins to view roster.
        if (SecurityService::isAdmin()) {
            return true;
        }
        
        $division = DivisionService::getItem($divisionid);
        
        $allowed_agegroups = UserService::getAssignedAgeGroups();
        
        foreach ($allowed_agegroups as $item) {
            if ($item->agegroup == $division->agegroup) {
                $retval = true;
            }
        }
        
        return $retval;
    }
}


