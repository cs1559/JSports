<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.0.0
 * @package     Tools.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Objects\Adapters\NSProAdapter;
use FP4P\Component\JSports\Site\Objects\Standings\StandingsEngine;
use FP4P\Component\JSports\Site\Services\ProgramsService;
// /use FP4P\Component\JSports\Site\Services\LogService;


class ToolsController extends BaseController
{
            
    protected $default_view = 'tools';
           
    public function display($cachable = false, $urlparams = array())
    {
        
        $input = Factory::getApplication()->input;       
        return parent::display($cachable, $urlparams);
        
    }
    
    public function newsletterImport($cachable = false, $urlparams = array()) {
        
        $app = Factory::getApplication();
        
        $adapter = new NSProAdapter();
        $contacts = $adapter->getContactsFromSource();
              
        $ctr = 0;
        foreach ($contacts as $contact) {
            
            if (!$adapter->alreadyOnList($contact['email'])) {
                //echo 'Email: ' . $contact['email'] . ' Found: ' . $adapter->alreadyOnList($contact['email']) . '<br/>';
                $adapter->addSubscriber($contact['firstname'] . ' ' . $contact['lastname'],$contact['email']);
                $ctr = $ctr + 1;
            }

        }
//         exit;
        
        $app->enqueueMessage("Newsletter list updated - " . $ctr . " emails added" , 'message');
        $this->setRedirect('index.php?option=com_jsports&view=tools');
   
    }
    
    public function purgeLogs($cachable = false, $urlparams = array()) {
        
        $params = ComponentHelper::getParams('com_jsports');
        $logdays = $params->get('logdays');
        
        $rows = LogService::purge($logdays);
        
        $msg = $rows . " log messages purged (Older than " . $logdays . ' days)';
        Factory::getApplication()->enqueueMessage($msg, 'message');
        LogService::info($msg);

        $this->setRedirect('index.php?option=com_jsports&view=tools');
        
    }
 
    public function refreshStandings($cachable = false, $urlparams = array()) {
        
        $params = ComponentHelper::getParams('com_jsports');   
        $salt = $params->get('cronkey');
        
        $app = Factory::getApplication();
        
        $output = new \stdclass();
        $output->datetime = date('l jS \of F Y h:i:s A');
        $output->content = "";
        
//         $input = Factory::getApplication()->input;
        $site = 'https://swibl.org';
        $content = '';
        
        $cron_key = md5('JSports Key For: ' . $site . $salt);
        
        ob_start();
        echo $output->datetime . "<br/>\n";
        print "Update Standings:  START <br/>\n";
        
        $engine = new StandingsEngine();
        
        // 2024-03-11 Made changes to support issue#8 - remove hardcoded value.
        // Retrieve the non completed programs.  'true' will filter only ACTIVE programs.
        $programs = ProgramsService::getNonCompletedPrograms(true);
        foreach ($programs as $program) {
            print "- Processing Program " . $program->id . "<br/>\n";
            $engine->generateStandings($program->id);
        }
        print "Update Standings:  END <br/>\n";
        $msize = ob_get_length();
//         header("Content-Length: $msize");
//         header('Connection: close');
        //$content = ob_get_contents();
        $content = ob_get_clean();
        
        //print $content;
        //$app->close();
        
        $msg = 'Standings were manually refreshed';
        Factory::getApplication()->enqueueMessage($msg, 'message');
        LogService::info("MANUAL REFRESH STANDINGS: <br/>\n" . $content);
        
        $this->setRedirect('index.php?option=com_jsports&view=tools');
        
        
        
        
    }
}
