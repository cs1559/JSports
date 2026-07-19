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

class VenuesController extends AdminController
{
    protected $default_view = 'Badges';
    
//     public function display($cachable = false, $urlparams = array())
//     {
        
//         return parent::display($cachable, $urlparams);
//     }

//     public function publish() {
//         $model = $this->getModel('Badge');
        
//         parent::publish();
//     }
    
    public function getModel($name = 'Badge', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
    
}
