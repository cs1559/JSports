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
    
}
