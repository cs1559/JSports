<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * 
 * Controller for TEAMS 
 *
 */

class TeamsController extends AdminController
{
    protected $default_view = 'Teams';
    
//     public function display($cachable = false, $urlparams = array())
//     {
        
//         return parent::display($cachable, $urlparams);
//     }
    
    public function publish() {
        $model = $this->getModel('Team');
        
        parent::publish();
    }
    
    public function getModel($name = 'Team', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

}
