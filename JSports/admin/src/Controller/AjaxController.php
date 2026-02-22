<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
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
     * Common function to properly reply the output of a given ajax function.
     * 
     * @param string $html
     */
    private function sendHtml(string $html): void
    {
        $app = Factory::getApplication();
        
        $app->setHeader('Content-Type', 'text/html; charset=utf-8', true);
        $app->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        $app->setHeader('Pragma', 'no-cache', true);
        
        echo $html;
        $app->close();
    }
    
    /**
     * This function is called by jQuery ajax calls to populate the options for a division
     * drop down list based on a selected Program id.
     */
    public function buildDivisionList() : void {

        // Session::checkToken('get') or jexit('Invalid Token');
        
        $input = Factory::getApplication()->input;
        $programid = $input->getInt('programid');
        
        $divisions = DivisionService::getDivisionList($programid);
        
        $options = "<option value=\"\">-- Select Division --</option>";
        
        foreach ($divisions as $division) {
            $id   = (int) ($division['id'] ?? 0);
            $name = htmlspecialchars((string) ($division['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            
            $options .= "<option value=\"{$id}\">{$name}</option>";
        }
        
        $this->sendHtml($options);
    }
    
    
    /**
     * This function is called by jQuery ajax calls to populate the options for a team
     * drop down list based on a selected Program id and division id.
     */
    public function buildTeamList() : void {
        // Session::checkToken('get') or jexit('Invalid Token');
        
        $input = Factory::getApplication()->input;
        $programid  = $input->getInt('programid');
        $divisionid = $input->getInt('divisionid');
        
        $teams = TeamService::getTeamList2($programid, $divisionid);
        
        $options = "<option value=\"\">-- Select Team --</option>";
        
        foreach ($teams as $team) {
            $id      = (int) ($team['teamid'] ?? 0);
            $teamname = htmlspecialchars((string) ($team['teamname'] ?? ''), ENT_QUOTES, 'UTF-8');
            $contact  = htmlspecialchars((string) ($team['contactname'] ?? ''), ENT_QUOTES, 'UTF-8');
            
            $label = trim($contact) !== '' ? "{$teamname} ({$contact})" : $teamname;
            
            $options .= "<option value=\"{$id}\">{$label}</option>";
        }
        
        $this->sendHtml($options);
        
    }
    
    /**
     * This function is called by jQuery ajax calls to populate the options for a GROUP
     * drop down list based on a selected Program id .
     */
    public function buildGroupList() : void {
        
        // Session::checkToken('get') or jexit('Invalid Token');
        
        $input = Factory::getApplication()->input;
        $programid = $input->getInt('programid');
        
        $groups = ProgramsService::getProgramGroups($programid);
        
        $options = "<option value=\"\">-- Select Group --</option>";
        
        foreach ($groups as $group) {
            $code = htmlspecialchars((string) ($group['code'] ?? ''), ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars((string) ($group['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            
            $options .= "<option value=\"{$code}\">{$name}</option>";
        }
        
        $this->sendHtml($options);
    }
}
