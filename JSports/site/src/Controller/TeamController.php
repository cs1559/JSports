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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Input\Input;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Component\ComponentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller object for an individiaul TEAM entry
 *
 * @since  1.6
 */
class TeamController extends BaseController
{
    
    public function display($cachable = false, $urlparams = [])
    {
        
        $app            = $this->app;
        $user           = $this->app->getIdentity();
        
        $params = ComponentHelper::getParams('com_jsports');
        $itemid = $params->get('itemid');

        parent::display($cachable = false, $urlparams = []);
    }
    
    /**
     * Method to save a TEAM object.
     *
     * @return  void|boolean
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function save()
    {
                
        // Check for request forgeries.
        $this->checkToken();

        $input = Factory::getApplication()->input;
        $origowner     = $input->getInt("origowner");
        
        $app    = $this->app;
        
        $logger = Myapp::getLogger();
        
        $model  = $this->getModel('Team', 'Site');
        $user   = $this->app->getIdentity();
        
        
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');

        $teamid = $requestData['id'];

        /* Code to prevent further action if user is NOT logged in */
        $user = Factory::getUser();
        // Check if the user is logged in
        if ($user->guest) {
            $app->enqueueMessage(Text::sprintf('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $requestData['id'], false));
            return false;
        }
        
        
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
            $app->setUserState('com_jsports.edit.team.data', $requestData);
            
            // Redirect back to the edit screen.
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&layout=edit&id=' .$teamid, false));
            
            return false;
        }

        $data['updatedby'] = $user->username;
        $data['dateupdated'] = date("Y-m-d H:i:s");
        
        // Attempt to save the data.
        $return = $model->save($data);
        
        $logger->info('TeamID: ' . $teamid. ' Team profile page UPDATED');
        
        if ($origowner != $requestData['ownerid']) {
            $myapp = myApp::getInstance();
            $myapp->triggerEvent('onAfterProfileOwnerUpdate', ['data' => $data, 'origowner' => $origowner, 'requestData' => $requestData]);
        }
        
        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_jsports.edit.team.data', $data);
            
            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('COM_JSPORTS_TEAM_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&layout=edit&id=' . $data['id'], false));
            
            return false;
        }
        
        // Redirect the user and adjust session state based on the chosen task.
        switch ($this->getTask()) {
//             case 'apply':
//                 // Check out the profile.
//                 $app->setUserState('com_users.edit.profile.id', $return);
                
//                 // Redirect back to the edit screen.
//                 $this->setMessage(Text::_('COM_USERS_PROFILE_SAVE_SUCCESS'));
                
//                 $redirect = $app->getUserState('com_users.edit.profile.redirect', '');
                
//                 // Don't redirect to an external URL.
//                 if (!Uri::isInternal($redirect)) {
//                     $redirect = null;
//                 }
                
//                 if (!$redirect) {
//                     $redirect = 'index.php?option=com_users&view=profile&layout=edit&hidemainmenu=1';
//                 }
                
//                 $this->setRedirect(Route::_($redirect, false));
//                 break;
                
            default:
                // Clear the profile id from the session.
                $app->setUserState('com_jsports.edit.team.data', null);
                
                $redirect = $app->getUserState('com_jsports.edit.team.redirect', '');
                
                // Don't redirect to an external URL.
                if (!Uri::isInternal($redirect)) {
                    $redirect = null;
                }
                
                if (!$redirect) {
                    $redirect = 'index.php?option=com_jsports&view=team&id=' .  $data['id'];
                }
                
                // Redirect to the Team Profile screen.
                $this->setMessage(Text::_('COM_JSPORTS_TEAM_SAVE_SUCCESS'));
                $this->setRedirect(Route::_($redirect, false));
                break;
        }
        
        // Flush the data from the session.
        $app->setUserState('com_jsports.edit.team.data', null);
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

        $app    = $this->app;
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array'); 
        $teamid = $requestData['id'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.team.data', null);
        
        // Redirect to user profile.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
    }
}
