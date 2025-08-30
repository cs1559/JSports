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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Objects\Validators\TeamValidator;


class AuditteamprofilesController extends AdminController
{
    protected $default_view = 'closeprogram';
    
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }
    
    public function cancel() {
        $this->setRedirect('index.php?option=com_jsports&view=tools');
    }
    
    public function process() {
       
        // Get ACtIVE programs only - by passing true in this call.
        $programs = ProgramsService::getNonCompletedPrograms(true);
        
        $tvalidator = new TeamValidator();
        
        foreach ($programs as $program) {
            echo $program->name;
            
            // NEED TO DELETE ALL AUDIT RECORDS FOR THIS PROGRAM
            
            $teams = TeamService::getTeamsByProgram($program->id);
            
            foreach ($teams as $team) {
                // EXECUTE VALIDATION RULES    
                if ($tvalidator->validate($team["teamid"])) {
                    echo $team["contactemail"] . " - is good <br/>";
                } else {
                    echo $team["contactemail"] . " - is bad - " . $tvalidator->msg . "<br/>";
                }
            }
        }
        exit;
        
        /*
        $input = Factory::getApplication()->input;
        $programid     = $input->getInt("programid");
        
            $programid = 33;
          
            $result = ProgramsService::closeProgram($programid);
            */
        
        $result = true;
             
            if ($result) {
                Factory::getApplication()->enqueueMessage("Team Profiles successfully audited - View Audit Results page for details", 'message');
            } else {
                Factory::getApplication()->enqueueMessage("An issue occurred when auditing the team profiles", 'warning');
            }
            
            $this->setRedirect('index.php?option=com_jsports&view=tools');
         
    }
    
    
}
