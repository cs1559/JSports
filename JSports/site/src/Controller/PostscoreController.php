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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Application;

use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Services\UserService;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller object for an individiaul GOLF entry
 *
 * @since  1.6
 */
class PostscoreController extends FormController
{
    /**
     * Displays the requested view; delegates to the parent FormController.
     *
     * NOTE: the parameters received here are currently ignored — the call
     * below always passes ($cachable = false, $urlparams = []) regardless of
     * what the caller passed in. If a caller ever needs caching enabled or
     * custom URL params honored, this will silently drop them. Consider
     * changing the call to `parent::display($cachable, $urlparams);`.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     *                               (currently not actually honored — see NOTE).
     * @param   array    $urlparams  Safe URL parameters (currently not
     *                               actually honored — see NOTE).
     *
     * @return  static  This object to support chaining.
     *
     * @since   1.6
     */
    public function display($cachable = false, $urlparams = [])
    {
        
        $app            = $this->app;        
        parent::display($cachable = false, $urlparams = []);
    }
    
    
    /**
     * Validates and saves a posted game score from 'jform' data, then
     * redirects back to the team's postscores list either way.
     *
     * @param   mixed  $key     Unused; present only for signature compatibility.
     * @param   mixed  $urlVar  Unused; present only for signature compatibility.
     *
     * @return  boolean|void  False if the user session is invalid, validation
     *                        fails, or the save fails. No explicit return
     *                        value on the success path.
     *
     * @throws  \Exception  If the model's form cannot be loaded.
     * @since   1.6
     */
    
    /* 12/21/2024 - renamed this function to 'save' .. formerly save2 */
    public function save($key = null, $urlVar = null)
    {
        
        $japp = Application::getInstance();
                   
        // Check for request forgeries.
        $this->checkToken();
       
        $app    = $this->app;
        $model  = $this->getModel('Postscore', 'Site');
        
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');

        //$gameid = $requestData['id'];
        $teamid = $requestData['teamid'];
        $redirectteamid = $requestData['redirectteamid'];
        
        /* Code to prevent further action if user is NOT logged in */
//         $user = Factory::getUser();
        $user = UserService::getUser();
        // Check if the user is logged in
        if ($user->guest) {
            $app->enqueueMessage(Text::sprintf('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=postscores&teamid=' . $redirectteamid, false));
            return false;
        }
        
        // Validate the posted data.
        $form = $model->getForm();
        
        if (!$form) {
            throw new \Exception($model->getError(), 500);
        }
        
        // Validate the posted data.
        $data = $model->validate($form, $requestData);
        $redirectUrl = 'index.php?option=com_jsports&view=postscores&teamid=' . $redirectteamid;
        
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
            $app->setUserState('com_jsports.edit.postscore.data', $requestData);

            // Redirect back to list of game scores to post.
            $this->setRedirect(Route::_($redirectUrl, false));
           
            return false;
        }
        
        // Attempt to save the data.
        $return = $model->save($data);
       
        //echo "PostscoreController::save -  after save";
        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_jsports.edit.postscore.data', $data);
            
            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('COM_JSPORTS_GAME_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_($redirectUrl, false));
            
            return false;
        }
        //$japp->triggerEvent('onAfterPostScore', ['data' => $data]);
        
        
        
        
//         // Flush the data from the session.
//         $app->setUserState('com_jsports.edit.postscore.data', null);
        $this->setMessage(Text::_('COM_JSPORTS_SCOREPOSTED_SUCCESS'));
        $this->setRedirect(Route::_($redirectUrl, false));

    }
      
    
    /**
     * Cancels a Postscore data-entry edit, clearing the session edit state
     * and redirecting back to the team's postscores list.
     *
     * @param   mixed  $key  Unused; present only for signature compatibility.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function cancel($key = null)
    {
        // Check for request forgeries.
        $this->checkToken();

        $app    = $this->app;
        // Get the team id.
        $requestData = $app->getInput()->post->get('jform', [], 'array'); 
        //$teamid = $requestData['teamid'];
        $redirectteamid = $requestData['redirectteamid'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.postscore.data', null);
                
        // Redirect to team schedule.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=postscores&teamid=' . $redirectteamid, false));
    }
}
