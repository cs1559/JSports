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
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use FP4P\Component\JSports\Site\Objects\Application;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\RegistrationService;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Authentication\Authentication;

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
     * Prepares the session state for a new registration and redirects to the
     * program's registration form.
     *
     * NOTE: registration currently only supports NEW registrations on the
     * frontend, despite the "edit" name.
     *
     * @param   mixed   $key     Unused; present only for signature compatibility.
     * @param   string  $urlVar  Unused; present only for signature compatibility.
     *
     * @return  boolean  Always true.
     *
     * @since   1.6
     */
    public function edit($key = null, $urlVar = 'id')
    {
        
        $app            = $this->app;
//         $user           = $this->app->getIdentity();
        $user = UserService::getUser();

        $registrationId = (int) $this->input->get('id');
        
        $params = ComponentHelper::getParams('com_jsports');
        $itemid = $params->get('itemid');
        
        $formdata = new Input($this->input->get('jform','','array'));
          
        // Locate the program ID from the form from the calling page.
        $programid = $formdata->get('programid');
       
        $svc = new ProgramsService();
        $program = $svc->getItem($programid);
        $options = json_decode($program->registrationoptions);
        
        // $layout = $program->registrationtemplate;
        $layout = $options->registrationtemplate;
        
        /**
         * If the program does not have a specific regisration template defined, then use the default
         */
        if (strlen($layout) < 1) {
            $layout = 'default';
        }
        
        //@TODO  Need to add check for the existence of the file.
        
        $vName = $this->input->get('view', 'registration');
        $this->input->set('view', $vName);
        
        // Set the registration id to edit in the session.
        $app->setUserState('com_jsports.edit.registration.id', $registrationId);
        $app->setUserState('com_jsports.edit.registration.programid', $programid);
        $app->setUserState('com_jsports.edit.registration.agreementurl', $options->agreementurl);
        
        // Redirect to the default screen.
        //$this->setRedirect(Route::_('index.php?option=com_jsports&view=registerteam&programid=' . $programid . '&Itemid=' . $itemid, false));
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=registration&layout=' . $layout . '&Itemid=' . $itemid, false));
        
        return true;
    }
    
    /**
     * Validates and saves a new team registration from posted 'jform' data,
     * logs the registration, fires the onAfterRegistration event, and
     * redirects to a completion page.
     *
     * @return  boolean|void  False if the form fails validation or the save
     *                        fails. No explicit return value on the success
     *                        path (redirects and clears session state instead).
     *
     * @throws  \Exception  If the model's form cannot be loaded.
     * @since   1.6
     */
    public function save()
    {
        
        $logger = Myapp::getLogger();
        $japp = Application::getInstance();
        
        // Check for request forgeries.
        $this->checkToken();
           
        $app    = $this->app;
        
        $model  = $this->getModel('Registration', 'Site');
//         $user   = $this->app->getIdentity();
        $user = UserService::getUser();
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
            $app->setUserState('com_jsports.registration.data', $requestData);
            
            // Redirect back to the edit screen.
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=register', false));
            
            return false;
        }
        
        // Attempt to save the data.
        $return = $model->save($data);
        $lastid = $return;
        
        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_jsports.registration.data', $data);
            
            $this->setMessage(Text::sprintf('COM_JSPORTS_REGISTRATION_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=dashboard', false));
            
            return false;
        }
        
        $logger->custom('Registration', 'Id: ' . $lastid . '  Team:' . $data['teamname'] . '  by ' . $data['registeredby'] . " Program: " . $data['programid'] . " Group: " . $data['grouping'] .
            ' Skill: ' . $data['skilllevel'] . ' IP ADDR: ' . $_SERVER['REMOTE_ADDR']);

        $japp->triggerEvent('onAfterRegistration', ['data' => $data, 'regid' => $lastid]);
        
        // Clear the profile id from the session.
        $app->setUserState('com_jsports.edit.registration.data', null);
        
        $redirect = $app->getUserState('com_jsports.edit.registration.redirect', '');
        
        // Don't redirect to an external URL.
        if (!Uri::isInternal($redirect)) {
            $redirect = null;
        }
        
        if (!$redirect) {
            //$redirect = 'index.php?option=com_jsports&view=dashboard';
            $redirect = 'index.php?option=com_jsports&view=registrationcomplete' . '&id=' . $lastid;
        }
                
        // Redirect to the list screen.
        $this->setMessage(Text::_('COM_JSPORTS_REGISTRATION_SAVE_SUCCESS'));
        $this->setRedirect(Route::_($redirect, false));
        
        // Flush the data from the session.
        $app->setUserState('com_jsports.registration.data', null);
        $app->setUserState('com_jsports.edit.registration.data', null);
    }
    
    /**
     * Cancels a registration in progress, clearing all related session state
     * and redirecting back to the first registration page.
     *
     * @return  void
     *
     * @since   1.6
     */
    public function cancel()
    {
        // Check for request forgeries.
        $this->checkToken();
        
        $app = $this->app ?? Factory::getApplication();
        
        // Flush the data from the session.
        $app->setUserState('com_jsports.edit.registrations', null);
        $app->setUserState('com_jsports.edit.registration.data', null);
        $app->setUserState('com_jsports.edit.registration.programid', null);
        
        // Redirect to register view.
        $this->setMessage(Text::_('COM_JSPORTS_OPERATION_CANCELLED'),'success');
        $this->setRedirect('index.php?option=com_jsports&view=register');
        return;
    }    
    
    
    
    /**
     * AJAX endpoint: verify credentials AND that this user is allowed
     * to register the given team.
     */
    public function checkteamauth()
    {
        $app   = Factory::getApplication();
        $input = $app->input;
        
        // CSRF check - required for any state-changing/ajax POST
        if (!Session::checkToken('post')) {
            $this->sendJson(false, Text::_('JINVALID_TOKEN'));
        }
        
        // Fields are nested under jform[] because they're rendered via JForm
        $jform = $input->post->get('jform', [], 'array');
        
        $returningTeam = isset($jform['returningteam']) ? (int) $jform['returningteam'] : 0;
        $username = isset($jform['username']) ? trim((string) $jform['username']) : '';
        $password = isset($jform['password']) ? (string) $jform['password'] : ''; // raw, don't filter
        $teamId   = isset($jform['teamid']) ? (int) $jform['teamid'] : 0;
        $lastprogramid   = isset($jform['lastprogramplayed']) ? (int) $jform['lastprogramplayed'] : 0;

        if ($returningTeam == 0) {
            $this->sendJson(true, 'Ok');
        }
        
        if ($returningTeam == 1 && ($username === '' || $password === '' || $teamId <= 0)) {
            $this->sendJson(false, 'Missing required fields.');
        }
        
        // Step 1: authenticate credentials
        $authenticate = Authentication::getInstance();
        $credentials  = ['username' => $username, 'password' => $password];
        $options      = ['action' => 'core.login.site'];
        $response     = $authenticate->authenticate($credentials, $options);
        
        if ($response->status !== Authentication::STATUS_SUCCESS) {
            $this->sendJson(false, 'Invalid username or password.');
        }
        
        // Step 2: resolve the user id
        $userId = \Joomla\CMS\User\UserHelper::getUserId($username);
        
        if (!$userId) {
            $this->sendJson(false, 'User not found.');
        }
        
//         // Step 3: check this user is allowed to register this team
//         /** @var JsportsModelRegistration $model */
//         $model = $this->getModel('Registration');
        
//         if (!$model->userCanRegisterTeam($userId, $teamId)) {
        if (!RegistrationService::canRegisterTeam($userId, $teamId, $lastprogramid)) {
            $this->sendJson(false, 'You are not authorized to register this team.');
        }
        
        $this->sendJson(true, 'OK');
    }
    
    /**
     * Helper: send JSON and terminate.
     */
    private function sendJson(bool $success, string $message): void
    {
        $app = Factory::getApplication();
        $app->setHeader('Content-Type', 'application/json');
        $app->sendHeaders();
        echo json_encode(['success' => $success, 'message' => $message]);
        $app->close();
    }
    
}
