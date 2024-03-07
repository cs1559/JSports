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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Input\Input;

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

        
        $app    = $this->app;
        
        $model  = $this->getModel('Team', 'Site');
        $user   = $this->app->getIdentity();
        
        
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');

        $teamid = $requestData['id'];
        
        // Validate the posted data.
        $form = $model->getForm();
        
        if (!$form) {
            throw new \Exception($model->getError(), 500);
        }
        
        // Send an object which can be modified through the plugin event
//         $objData = (object) $requestData;
//         $app->triggerEvent(
//             'onContentNormaliseRequestData',
//             ['com_users.user', $objData, $form]
//             );
//         $requestData = (array) $objData;
        
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
        
        // Attempt to save the data.
        $return = $model->save($data);
        
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
