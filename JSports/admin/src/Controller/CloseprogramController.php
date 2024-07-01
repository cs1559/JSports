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
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;


class CloseprogramController extends AdminController
{
    protected $default_view = 'closeprogram';
    
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }
    
    public function cancel() {
        $this->setRedirect('index.php?option=com_jsports&view=programs');
    }
    
    public function process() {

        $input = Factory::getApplication()->input;
        $programid     = $input->getInt("programid");
        
            $programid = 33;
            
            $result = ProgramsService::closeProgram($programid);
             
            if ($result) {
                Factory::getApplication()->enqueueMessage("Program closed", 'message');
            } else {
                Factory::getApplication()->enqueueMessage("An issue occurred when closing program", 'warning');
            }
            $this->setRedirect('index.php?option=com_jsports&view=programs');
    }
    
    
}
