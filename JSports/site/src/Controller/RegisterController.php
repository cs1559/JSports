<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use FP4P\Component\JSports\Site\Objects\Application;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;


use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Component\ComponentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller object for an individiaul REGISTRATION entry
 *
 * @since  1.6
 */
class RegisterController extends BaseController
{
    
    
    /**
     * Cancels an in-progress registration, clearing the session edit state
     * and redirecting to the site root.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function cancel()
    {
        // Check for request forgeries.
        $this->checkToken();
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.registrations', null);
        
        // Redirect to register view.
        $this->setMessage(Text::_('COM_JSPORTS_OPERATION_CANCELLED'),'success');
//         $this->setRedirect(Route::_('index.php?option=com_jsports&view=register', false));
        $this->setRedirect('index.php');
    }
    
    /**
     * Terminal step of the registration flow: prints a confirmation message
     * and ends the request.
     *
     * @return  void  Never returns — always terminates via exit.
     *
     * @since   1.6
     */
    public function complete() {
        echo "Registration complete";
        exit;
    }
        
}
