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

class RegistrationsController extends AdminController
{
    protected $default_view = 'registrations';
    
    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }
    
    public function getModel($name = 'Registration', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function publish() {
        /* Set the model that is used in the process of registrations.  Forcing the registration model
         * to be retrieved ensures that that PUBLISH event that is called is within that particular model.
         * 
         * In this instance, the registration model calls a function that processes each individual registration
         * selected. 
         */
        
        $model = $this->getModel('Registration');
        
        parent::publish();
    }
}
