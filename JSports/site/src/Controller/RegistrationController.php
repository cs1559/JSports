<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
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
use Joomla\CMS\Input\Input;
use FP4P\Component\JSports\Site\Objects\Application;


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
class RegistrationController extends BaseController
{
    
    
    
    /**
     * Method to check out a user for editing and redirect to the edit form.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    public function edit($key = null, $urlVar = 'id')
    {
        
        $app            = $this->app;
        $user           = $this->app->getIdentity();
        $registrationId = (int) $this->input->get('id');
        
        
        $params = ComponentHelper::getParams('com_jsports');
        $itemid = $params->get('itemid');
              
        
        $formdata = new Input($this->input->get('jform','','array'));
        
        // Locate the program ID from the form from the calling page.
        $programid = $formdata->get('programid');
        $svc = new ProgramsService();
        $program = $svc->getItem($programid);
        
        $layout = $program->registrationtemplate;
        
        if (strlen($layout) < 1) {
            $layout = 'edit';
        }
        
        //@TODO  Need to add check for the existence of the file.
        
        $vName = $this->input->get('view', 'registration');
        $this->input->set('view', $vName);
                
        // Set the registration id to edit in the session.
        $app->setUserState('com_jsports.edit.registration.id', $registrationId);
        $app->setUserState('com_jsports.edit.registration.programid', $programid);
        $app->setUserState('com_jsports.edit.registration.agreementurl', $program->agreementurl);
        
        // Redirect to the edit screen.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=registration&layout=' . $layout . '&Itemid=' . $itemid, false));
        
        return true;
    }
    
    /**
     * Method to save a user's profile data.
     *
     * @return  void|boolean
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function save()
    {
                
        $app = Application::getInstance();
        
        // Check for request forgeries.
//         $this->checkToken();

        
        $app    = $this->app;
        
        $model  = $this->getModel('Registration', 'Site');
        $user   = $this->app->getIdentity();
        $userId = (int) $user->get('id');
        
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
        
        
        // Validate the posted data.
        $form = $model->getForm();
        
        if (!$form) {
            throw new \Exception($model->getError(), 500);
        }
        
        // Validate the posted data.
        $data = $model->validate($form, $requestData);
        
        // Check for errors.
        if ($data === false) {
            // Get the validation messages.
            $errors = $model->getErrors();
            
            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof \Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }
                      
            // Save the data in the session.
            $app->setUserState('com_jsports.edit.registration.data', $requestData);
            
            // Redirect back to the edit screen.
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=register', false));
            
            return false;
        }
        
        // Attempt to save the data.
        $return = $model->save($data);
        
        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_jsports.edit.registration.data', $data);
            
            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('COM_JSPORTS_REGISTRATION_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=dashboard', false));
            
            return false;
        }

        $app->triggerEvent('onAfterRegistration', ['data' => $data]);
        
        // Redirect the user and adjust session state based on the chosen task.
        switch ($this->getTask()) {
            default:
                // Clear the profile id from the session.
                $app->setUserState('com_jsports.edit.registration.data', null);
                
                $redirect = $app->getUserState('com_jsports.edit.registration.redirect', '');
                
                // Don't redirect to an external URL.
                if (!Uri::isInternal($redirect)) {
                    $redirect = null;
                }
                
                if (!$redirect) {
                    $redirect = 'index.php?option=com_jsports&view=dashboard';
                }
                
                // Redirect to the list screen.
                $this->setMessage(Text::_('COM_JSPORTS_REGISTRATION_SAVE_SUCCESS'));
                $this->setRedirect(Route::_($redirect, false));
                break;
        }
        
        // Flush the data from the session.
        $app->setUserState('com_jsports.edit.registration.data', null);
    }
    
    /**
     * Method to cancel an edit.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function cancel()
    {
        // Check for request forgeries.
        $this->checkToken();
        
        // Flush the data from the session.
        $this->app->setUserState('com_users.edit.profile', null);
        
        // Redirect to user profile.
        $this->setRedirect(Route::_('index.php?option=com_users&view=profile', false));
    }
}
