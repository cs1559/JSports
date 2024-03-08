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

use Joomla\CMS\MVC\Controller\BaseController;
use FP4P\Component\JSports\Site\Objects\Standings\StandingsEngine;
   
class DisplayController extends BaseController
{
            
    protected $default_view = 'dashboard';
           
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }
    
    
    public function dashboard($cachable = false, $urlparams = array()) {
        $this->setRedirect('index.php?option=com_jsports&view=dashboard');
    }
    
}