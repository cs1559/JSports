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

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\DivisionService;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
   
/**
 * The AjaxController supports a series of functions used in AJAX calls by the front-end side of the component 
 * to the backend to retrieve data.   Most functions will return content that updates the html value of an html element.
 * @author cs155
 *
 */
class AjaxController extends BaseController
{
            
    /**
     * This function is called by jQuery ajax calls to populate the options for a division
     * drop down list based on a selected Program id.
     */
    public function buildDivisionList() {
        
        $input = Factory::getApplication()->input;
        $programid     = $input->getInt("programid");
        
        $divisions = DivisionService::getDivisionList($programid);
        
        $options  = "<option value=''>-- Select Division -- </option>";
        foreach ($divisions as $division) {
            $options = $options . "<option value='" . $division['id'] . "'>" . $division['name'] . "</option>";
        }
        echo $options;
    }
    
    
    /**
     * This function is called by jQuery ajax calls to populate the options for a team
     * drop down list based on a selected Program id and division id.
     */
    public function buildTeamList() {
        
        $input = Factory::getApplication()->input;
        $programid     = $input->getInt("programid");
        $divisionid     = $input->getInt("divisionid");
        
        $teams = TeamService::getTeamList2($programid, $divisionid);
        
        $options  = "<option value=''>-- Select Team -- </option>";
        foreach ($teams as $team) {
            $options = $options . "<option value='" . $team['teamid'] . "'>" . 
                $team['teamname'].' (' .$team['contactname'].')' . "</option>";
        }
        echo $options;
    }
    
    /**
     * This function is called by jQuery ajax calls to populate the options for a GROUP
     * drop down list based on a selected Program id .
     */
    public function buildGroupList() {
        
        $input = Factory::getApplication()->input;
        $programid     = $input->getInt("programid");
        
        $groups = ProgramsService::getProgramGroups($programid);
        
        $options  = "<option value=''>-- Select Group -- </option>";
        foreach ($groups as $group) {
            $options = $options . "<option value='" . $group['code'] . "'>" .
                $group['name'] . "</option>";
        }
        echo $options;
    }
}
