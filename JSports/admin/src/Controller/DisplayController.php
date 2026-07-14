<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;  

class DisplayController extends BaseController
{
            
    protected $default_view = 'dashboard';
               
    public function dashboard($cachable = false, $urlparams = array()) {
        $this->setRedirect('index.php?option=com_jsports&view=dashboard');
        $this->redirect();
        
        return true;
    }
    
}
