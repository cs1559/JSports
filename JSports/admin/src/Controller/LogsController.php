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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\LogService;
use Joomla\CMS\Router\Route;

class LogsController extends AdminController
{
    protected $default_view = 'logs';   

    /**
     * This function supports the purging of the log records so it doesn't become unnecessarily 
     * too large.  The number of days is a configurable option within the component.
     */
    public function purge() : bool {
        
        $this->checkToken();
        
        // Retrieve the number of logdays to retain
        $params = ComponentHelper::getParams('com_jsports');
        $logdays = (int) $params->get('logdays', 30);
       
        $rows = LogService::purge($logdays);
        
        $msg = $rows . " log messages purged (Older than " . $logdays . ' days)';
        $this->app->enqueueMessage($msg, 'message');
        LogService::info($msg);
        
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=logs', false));
        return true;
    }
    
}
