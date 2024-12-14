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
 * ================================================================================
 * LEAGUES MAY NOT BE REQUIRED AND MAYBE REMOVED IN A LATER RELASE.  MANY OF THE
 * OPTIONS ASSOCIATED WITH A LEAGUE ARE SUPPORTED AT THE PROGRAM LEVEL OR AT THE 
 * COMPONENT LEVEL.
 * ================================================================================ 
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\LogService;

class LogsController extends AdminController
{
    protected $default_view = 'leagues';
    
    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }


    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  The array of possible config values. Optional.
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     *
     * @since   1.6
     */
    public function getModel($name = 'Logs', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
    

    public function purge() {
        $params = ComponentHelper::getParams('com_jsports');
        $logdays = $params->get('logdays');
        
        $db    = Factory::getDbo();
        
        $query = $db->getQuery(true);
        
        $sql = "delete from " . $db->quoteName("#__jsports_action_logs")
        . " where logdate < (curdate() - interval " . $logdays . " day)";
        
        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();
        
        $rows = $db->getAffectedRows();
        
        $msg = $rows . " log messages purged (Older than " . $logdays . ' days)';
        Factory::getApplication()->enqueueMessage($msg, 'message');
        LogService::info($msg);
        
        $this->setRedirect('index.php?option=com_jsports&view=logs');
    }
    
}
