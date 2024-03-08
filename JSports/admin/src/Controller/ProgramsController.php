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

class ProgramsController extends AdminController
{
    protected $default_view = 'programs';
    
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }

    
    public function publish() {
        $model = $this->getModel('Program');
        
        parent::publish();
    }

    public function getModel($name = 'Program', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
    
}
