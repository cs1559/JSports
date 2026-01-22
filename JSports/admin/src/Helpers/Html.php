<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @deprecated
 */

namespace FP4P\Component\JSports\Administrator\Helpers;

use Joomla\CMS\Router\Route;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseInterface;

/**
 * The Html class is a helper class that returns various HTML elements.
 * @deprecated
 *
 */
class Html
{

    /**
     *
     */
    public static function getProgramDivisions($programid, $itemkey=0, $defaultvalue=0) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        
        $conditions = array(
            $db->quoteName('programid') . ' = ' . $db->quote($programid),
            $db->quoteName('published') . ' = 1 ',
        );
        
        $query->select($db->quoteName(array('id', 'name', 'agegroup')));
        $query->from($db->quoteName('#__jsports_divisions'));
        $query->where($conditions);
        $query->order('name ASC');
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectList();
                
        $html =  "
            <div id=\"division-list-" . $itemkey . "\">
            <select class=\"form-select\" name=\"div-assignment[" . $itemkey . "]\" id=\"divassignment-" . $itemkey . "\">
        ";
        
        $options = "<option value=\"\">" . "- Select -" . "</option>";
        foreach ($results as $result) {
            
            if ($defaultvalue == $result->id) {
                $options = $options .  "<option value=\"" . $result->id . "\" selected>" . $result->name . "</option>";
            } else {
                $options = $options .  "<option value=\"" . $result->id . "\">" . $result->name . "</option>";
            }
        }
        
        $html = $html . $options;
        
        $html = $html .  "</select></div>";
        return $html;
    }
    
    
    /**
     * This function will return the actions menu on the team profile that will enable the user to perform
     * certain functions.  The return string will be actual HTML code.
     *
     * NOTE:  There is a JQuery selector that attaches to this SELECT element for an "onChange" event.
     *
     * @param number $id
     * @return string
     */
    public static function getTeamProfileMenu($id = 0, $label="Actions", $class="form-select") {
        
        $params = ComponentHelper::getParams('com_jsports');
        
        $html =  "<div id=\"profile-actions-menu\">";
        if (strlen($label) > 0) {
            $html = $html . "<label for=\"profile-actions\" id=\"profile-actions-label\">" . $label . ":</label>";
        }
        $html = $html . "<select name=\"profile-actions\" class=\"" . $class . "\" id=\"profile-actions\"> ";
        
        $menuOptions = array();
        $menuOptions["Edit Team Profile"] = Route::_('index.php?option=com_jsports&view=team&layout=edit&id=' . $id);
        $menuOptions["Manage Roster"] = Route::_('index.php?option=com_jsports&view=rosters&teamid=' . $id);
        $menuOptions["Manage Schedule"] = Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $id);
        $menuOptions["Post Score(s)"] = Route::_('index.php?option=com_jsports&view=postscores&teamid=' . $id);
        $menuOptions["Upload Logo"] = Route::_('index.php?option=com_jsports&view=logoupload&teamid=' . $id);
        
        $enablebulletins = $params->get('enablebulletins');
        if ($enablebulletins) {
            $menuOptions["Manage Bulletins"] = Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $id);
        }
        
        
        $options = "<option value=\"\">" . "-- Select Action--" . "</option>";
        foreach ($menuOptions as $key => $value) {
            
            $options = $options .  "<option value=\"" . $value . "\">" . $key . "</option>";
        }
        
        $html = $html . $options;
        
        $html = $html .  "</select></div>";
        return $html;
    }
    
    /**
     * @deprecated
     * @param number $teamid
     * @param number $programid
     */
    public static function getDivisionalOpponents($teamid, $programid){
  
        throw new \Exception('Undefine function');
    }
    
    
    public static function getHomeTeamlist($teamid, $programid) {
        $id="hometeam-list";
        $class="form-select";
        $label="";
        
        return self::getTeamOpponents($teamid, $programid, $id, $label, $class);
    }

    public static function getAwatTeamlist($teamid, $programid) {
        $id="awayteam-list";
        $class="form-select";
        $label="";
        
        return self::getTeamOpponents($teamid, $programid, $id, $label, $class);
    }
    
    public static function getTeamOpponents($teamid, $programid, $id="teamlist", $label="Team", $class="form-select") {

        
        $rows = self::getOpponentData($teamid, $programid);
        
        $html =  "<div id=\"schedule-opponents-list-container\">";
        

        $html = $html . "<select name=\"" . $id . "\" class=\"" . $class . "\" id=\"" . $id  . "\"> ";
        
        $menuOptions = array();
        
        foreach ($rows as $item) {
            $menuOptions[$item['teamname']] = $item['teamid'];
        }
        $options = "<option value=\"\">" . "-- Select Action--" . "</option>";
        foreach ($menuOptions as $key => $value) {
            
            $options = $options .  "<option value=\"" . $value . "\">" . $key . "</option>";
        }
        
        $html = $html . $options;
        
        $html = $html .  "</select></div>";
        return $html;
            
        
    }
    
    protected static function getOpponentData($teamid, $programid) {

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, d.agegroup FROM " .
            $db->quoteName('#__jsports_teams') . " as a, " .
            $db->quoteName('#__jsports_map') . " as m, " .
            $db->quoteName('#__jsports_divisions') . " as d
            where m.teamid = a.id
            and m.divisionid = d.id
            and m.divisionid in (
                select divisionid from #__jsports_map as m, #__jsports_divisions as d
                where m.divisionid = d.id
                and m.teamid = " . $db->quote($teamid) . " and m.programid = " . $db->quote($programid) . "
                )";
            $query->setQuery($sql);
            //        $query->bind(':teamid', $teamid, ParameterType::INTEGER);
            //      $query->bind(':programid', $programid, ParameterType::INTEGER);
            $db->setQuery($query);
            
            // Load the results as a list of stdClass objects (see later for more options on retrieving data).
            return $db->loadAssocList();

    }
    
    /**
     * getProgramsList - this function will return an HTML select list for all of the programs
     * in the database.
     *
     * @param number $defaultvalue
     * @return string
     */
    public static function getProgramsList($name="program-list", $defaultvalue=0) {
        
        $params = ComponentHelper::getParams('com_jsports');
        $activeonly = $params->get('activestandingsonly');
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $conditions = array(
            $db->quoteName('registrationonly') . ' = 0',
            $db->quoteName('published') . ' = 1 ',
        );
        
        if ($activeonly) {
            array_push($conditions, 'status = \'A\'');
        }
        
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select($db->quoteName(array('id', 'name', 'status')));
        $query->from($db->quoteName('#__jsports_programs'));
        $query->where($conditions);
        $query->order('id desc');
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectList();
        
        $html =  "
            <div id=\"program-list\">
            <select class=\"form-select\"  name=\"" . $name . "\" id=\"" . $name . "\">
        ";
        
        $options = "<option value=\"\">" . "- Select Program -" . "</option>";
        foreach ($results as $result) {
            
            if ($result->status == "A") {
                $result->name = $result->name . " (active)";
            }
            if ($result->status == "P") {
                $result->name = $result->name . " (pending)";
            }
            
            
            if ($defaultvalue == $result->id) {
                $options = $options .  "<option value=\"" . $result->id . "\" selected>" . $result->name . "</option>";
            } else {
                $options = $options .  "<option value=\"" . $result->id . "\">" . $result->name . "</option>";
            }
        }
        
        $html = $html . $options;
        
        $html = $html .  "</select></div>";
        return $html;
    }
    
}

