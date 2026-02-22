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
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;


class CloseprogramController extends AdminController
{
    protected $default_view = 'closeprogram';
    
    /**
     * This function supports the CANCEL button that is on the Programs view
     * 
     * @param mixed $key
     * @return boolean
     */   
    public function cancel($key = null) : bool {
        
        $this->checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
//         parent::cancel($key);
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs', false));
        return true;
    }
    
    /**
     * The process function will validate the programid and execute the closeprogram function that is 
     * part of the ProgramsService service.
     * 
     * @return bool
     */
    public function process() : bool {

        $this->checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
//         $input = Factory::getApplication()->input;
        $app = Factory::getApplication();
        $programid     = $this->input->getInt("programid");
            
        if ($programid <= 0) {
            $app->enqueueMessage('Invalid program id.', 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs', false));
            return false;
        }
        
        $result = ProgramsService::closeProgram($programid);
             
        if ($result) {
            $app->enqueueMessage("Program closed", 'message');
        } else {
            $app->enqueueMessage("An issue occurred when closing program", 'warning');
        }
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs', false));
        return (bool) true;
    }
    
}
