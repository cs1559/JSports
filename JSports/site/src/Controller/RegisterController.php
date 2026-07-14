<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace FP4P\Component\JSports\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
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
 * Controller object for processing a Registration.  Register is the first step in the registration process
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
//         $this->app->setUserState('com_jsports.edit.registrations', null);
        $this->app->setUserState('com_jsports.registration.data', null);
        
        // Redirect to register view.
        $this->setMessage(Text::_('COM_JSPORTS_OPERATION_CANCELLED'),'success');
//         $this->setRedirect(Route::_('index.php?option=com_jsports&view=register', false));
        $this->setRedirect('index.php');
    }
    
    
    public function save($key = null, $urlVar = null)
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();
        
        // Adjust 'jform' if your <form> layout uses a different form control name
        $data = $input->post->get('jform', [], 'array');
        
        if (!Session::checkToken()) {
            $app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
            $this->setRedirect('index.php?option=com_jsports&view=register');
            return false;
        }
        
        // Keep the whole submitted array around for the next step
        $app->setUserState('com_jsports.registration.data', $data);
        
        // Redirect to STEP 2 page.
        $this->setRedirect('index.php?option=com_jsports&view=registration');
        
        return true;
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
