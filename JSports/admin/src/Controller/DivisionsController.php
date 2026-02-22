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


class DivisionsController extends AdminController
{
    protected $default_view = 'divisions';
    
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
     */
    public function getModel($name = 'Division', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
    
    public function publish() {
        $model = $this->getModel('Division');
        
        parent::publish();
    }
}
