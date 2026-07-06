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

use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\UserService;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * GameController - This is a controller class that handles GAME functions.  Most
 * of the functions are inherited from the parent class (FormController) but certain
 * functions are overridden in this class.
 *
 */
class GameController extends FormController
{
    
    /**
     * Displays the requested view; delegates entirely to the parent
     * FormController implementation.
     *
     * @param   boolean  $cachable   If true, the view output will be cached.
     * @param   array    $urlparams  Safe URL parameters and their variable types.
     *
     * @return  static  This object to support chaining.
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = [])
    {
        return parent::display($cachable, $urlparams);
    }
    
    /**
     * Deletes a game from a team's schedule, provided the user has a valid
     * session and the game is not already marked complete.
     *
     * Expects 'id' (game id) and 'teamid' (or legacy 'contextid') request
     * parameters.
     *
     * @return  boolean  True if the game was deleted successfully, false if
     *                   validation failed, the game couldn't be deleted, or
     *                   an exception occurred.
     *
     * @since   1.0.0
     */
    public function delete() {

        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $logger = Myapp::getLogger();
        $app    = Factory::getApplication();
        $input  = $app->input;
        $id     = $input->getInt('id');
        //@TODO  Context ID needs to be changed to Team ID for Game/Schedule management
        $contextid = $input->getInt('contextid');
        $teamid    = $input->getInt('teamid', $contextid);
        $user   = UserService::getUser();
        
//         $method = strtoupper($this->input->getMethod());
        
//         if ($method === 'GET') {
//             $this->checkToken('get');
//         } else {
//             // Check for request forgeries.
//             $this->checkToken();
//         }
        
//         $lasturl = $_SERVER['HTTP_REFERER'];
//         if ($lastUrl && Uri::isInternal($lastUrl)) {
//             $this->setRedirect($lastUrl);
//         }
        
        // Check if the user is logged in
        if ($user->guest) {
            $app->enqueueMessage(Text::sprintf('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $logger->info('Game ID: ' . $id. ' Game DELETE failed due to user session invalid');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $teamid, false));
            return false;
        }
     
        // Get the record to be deleted so you know the team ID and the program ID.
        $svc = new GameService();
        $item = $svc->getItem($id);
        
        if (!$item || $id == 0) {
            $this->setMessage(Text::_('COM_JSPORTS_GAME_INVALID_ID_DELETE_FAILED'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=dashboard', false));
            return false;
        }

        if ($item->gamestatus === 'C') {
            $this->setMessage(Text::_('COM_JSPORTS_GAME_CANNOT_BE_DELETED'), 'info');
            return $this->setRedirect(
                Route::_('index.php?option=com_jsports&view=schedules&teamid=' . (int) $teamid . '&programid=' . (int) $item->programid, false)
                );
        }
        
        $redirectURL = 'index.php?option=com_jsports&view=dashboard';
        $rUrl = 'index.php?option=com_jsports&view=schedules&teamid=' .
            (int) $teamid . '&programid=' . (int) $item->programid;

        try {
            $result = GameService::delete($id);
            if ($result) {
                $logger->info('Game ID: ' . $id. ' has been DELETED  ' . $item->gamedate . ' ' . $item->name . ' STATUS=' . $item->gamestatus);
                $this->setMessage(Text::_('COM_JSPORTS_GAME_SUCCESSFULLY_DELETED'),'success');
            } else {
                $logger->error('Game ID: ' . $id. ' has NOT been deleted' );
                $this->setMessage(Text::_('COM_JSPORTS_GAME_NOT_DELETED'),'error');
            }
            $redirectURL = $rUrl;
            
            
            $this->setRedirect(Route::_($redirectURL, false));
            return (bool) $result;
            
        } catch (\Exception $e) {
            $errors = $item->getErrors();
            $this->setError($errors[0]);
            $app->enqueueMessage($errors[0],'error');
            $redirectURL = 'index.php?option=com_jsports&view=schedules&teamid=' .
                (int) $teamid . '&programid=' . (int) $item->programid;
            $logger->error('Game ID: ' . $id. ' has NOT been deleted' );
            $this->setMessage(Text::_('COM_JSPORTS_GAME_NOT_DELETED'),'error');
                
            $this->setRedirect(Route::_($redirectURL, false));
                return false;
        }

//         $this->setRedirect(Route::_($redirectURL, false));
        
    }

    
    
    /**
     * Resets a game's status back to 'Scheduled' ('S'), e.g. to undo an
     * accidental completion or cancellation.
     *
     * Expects 'id' (game id) and 'teamid' request parameters.
     *
     * @return  boolean  True if the reset succeeded, false if validation
     *                   failed or the reset was unsuccessful.
     *
     * @since   1.0.0
     */
    public function reset() {
        
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $logger = Myapp::getLogger();
        $app = Factory::getApplication();
        
        $input = $app->getInput();
        $id     = $input->getInt("id");
        $teamid = $input->getInt("teamid");
        
        /* Code to prevent further action if user is NOT logged in */
        $user = UserService::getUser();
        
        // Check if the user is logged in
        if ($user->guest) {
            $app->enqueueMessage(Text::sprintf('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $logger->info('Game ID: ' . $id. ' Game RESET failed due to user session invalid');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $teamid, false));
            return false;
        }
        
        if ($id == 0) {
            $this->setMessage(Text::_('COM_JSPORTS_GAME_INVALID_ID_RESET_FAILED'),'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=dashboard', false));
            return false;
        }
        
        // Get the record to be reset so you know the team ID and the program ID.
        $svc = new GameService();
        $item = $svc->getItem($id);
        
        $redirectURL = 'index.php?option=com_jsports&view=dashboard';
        $rUrl = 'index.php?option=com_jsports&view=schedules&teamid=' . (int) $teamid
                . '&programid=' . (int) $item->programid;
        try {
            $result = GameService::reset($id);
            if ($result) {
                $logger->info('Game ID: ' . $id . ' status has been reset');
                $this->setMessage(Text::_('COM_JSPORTS_GAME_RESET_SUCCESSFUL'),'success');
            } else {
                $this->setMessage("Game status was NOT reset",'info');
            }
            $redirectURL = $rUrl;
            
        } catch (\Exception $e) {
            $errors = $item->getErrors();
            $this->setError($errors[0]);
            $app->enqueueMessage($errors[0],'error');
            // FIX: Changed $itemid to $teamid
            $redirectURL = 'index.php?option=com_jsports&view=schedules&teamid=' .
                $teamid . '&programid=' . $item->programid;
        }
        
        $this->setRedirect(Route::_($redirectURL, false));
        return true;
    }
    
//     /**
//      * Function to support the RESET of a game from the team schedule.  This function resets the status to 'S'
//      * @return boolean
//      */
//     public function reset() {
        
//         $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
//         $logger = Myapp::getLogger();
//         $app = Factory::getApplication();
        
//         $input = Factory::getApplication()->input;
//         $id     = $input->getInt("id");
//         $teamid     = $input->getInt("teamid");
        
//         /* Code to prevent further action if user is NOT logged in */
// //         $user = Factory::getUser();
//         $user = UserService::getUser();
//         // Check if the user is logged in
//         if ($user->guest) {
//             $app->enqueueMessage(Text::sprintf('COM_JSPORTS_INVALID_USERSESSION'), 'error');
//             $logger->info('Game ID: ' . $id. ' Game RESET failed due to user session invalid');
//             $this->setRedirect(Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $teamid, false));
//             return false;
//         }
        
//         if ($id == 0) {
//             $this->setMessage(Text::_('COM_JSPORTS_GAME_INVALID_ID_RESET_FAILED'),'error');
//             $this->setRedirect(Route::_('index.php?option=com_jsports&view=dashboard', false));
//             return false;
//         }
        
//         // Get the record to be reset so you know the team ID and the program ID.
//         $svc = new GameService();
//         $item = $svc->getItem($id);
        
//         $redirectURL = 'index.php?option=com_jsports&view=dashboard';
// //         $rUrl = 'index.php?option=com_jsports&view=schedules&teamid=' . $item->teamid . '&programid=' . $item->programid;
//         $rUrl = 'index.php?option=com_jsports&view=schedules&teamid=' . $teamid . '&programid=' . $item->programid;
//             try {
//                 $result = GameService::reset($id);
//                 if ($result) {
//                     $logger->info('Game ID: ' . $id . ' status has been reset');
//                     $this->setMessage(Text::_('COM_JSPORTS_GAME_RESET_SUCCESSFUL'),'info');
//                 } else {
//                     $this->setMessage("Game status was NOT reset",'info');
//                 }
//                 $redirectURL = $rUrl;
                
//             } catch (\Exception $e) {
//                 $errors = $item->getErrors();
//                 $this->setError($errors[0]);
//                 $app->enqueueMessage($errors[0],'error');
//                 $redirectURL = 'index.php?option=com_jsports&view=schedules&teamid=' .
//                     $itemid   . '&programid=' . $item->programid;
//             }
        
            
//         $this->setRedirect(Route::_($redirectURL), false);
        
//     }
    
    
    
    /**
     * Validates and saves a game item (create or update) from posted
     * 'jform' data, then redirects based on the outcome and the current task.
     *
     * @param   mixed  $key     Unused; present only for signature compatibility.
     * @param   mixed  $urlVar  Unused; present only for signature compatibility.
     *
     * @return  boolean|void  False if the user session is invalid, the form
     *                        fails validation, or the save fails. No explicit
     *                        return value on the success path (redirects and
     *                        clears session state instead).
     *
     * @throws  \Exception  If the model's form cannot be loaded.
     * @since   1.0.0
     */
    public function save($key = null, $urlVar = null)
    {
                
        // Check for request forgeries.
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $app    = $this->app;
        $model  = $this->getModel('Game', 'Site');

        
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');

        $gameid = $requestData['id'];
        $teamid = $requestData['teamid'];
        $programid = $requestData['programid'];
        $contextid = $requestData['contextid'];
        
        /* Code to prevent further action if user is NOT logged in */
//         $user = Factory::getUser();
        $user = UserService::getUser();
        // Check if the user is logged in
        if ($user->guest) {
            $app->enqueueMessage(Text::sprintf('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $teamid, false));
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
            $app->setUserState('com_jsports.edit.game.data', $requestData);

            // Redirect back to the edit screen.
            // @TODO  Need to look at how the context id is set
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=game&layout=edit&id=' .$gameid . '&teamid=' . $teamid . '&programid=' . $programid, false));
            //$this->setRedirect(Route::_('index.php?option=com_jsports&view=game&layout=edit&id=' .$gameid . '&teamid=' . $contextid, false));
            
            return false;
        }
        
        // Attempt to save the data.
        $return = $model->save($data);
        
        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_jsports.edit.game.data', $data);
            
            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('COM_JSPORTS_GAME_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(
                Route::_('index.php?option=com_jsports&view=game&layout=edit&id=' . $gameid . '&teamid=' . $data['teamid']
                    , false));
            
            return false;
        }
        
        // Redirect the user and adjust session state based on the chosen task.
        switch ($this->getTask()) {
                
            default:
                // Clear the game data from the session.
                $app->setUserState('com_jsports.edit.game.data', null);
                
                $redirect = $app->getUserState('com_jsports.edit.games.redirect', '');
                
                // Don't redirect to an external URL.
                if (!Uri::isInternal($redirect)) {
                    $redirect = null;
                }
                
                if (!$redirect) {
                    $redirect = 'index.php?option=com_jsports&view=schedules&teamid=' .  $data['teamid'] .
                        '&programid='  . $data['programid'];
                    //@TODO  need to look at.
                    //$redirect = 'index.php?option=com_jsports&view=schedules&teamid=' .  $data['contextid'] .
//                     '&programid='  . $data['programid'];
                }
                

                // Redirect to the Team Profile screen.
                $this->setMessage(Text::_('COM_JSPORTS_GAME_SAVE_SUCCESS'));
                $this->setRedirect(Route::_($redirect, false));
                break;
        }
        
        // Flush the data from the session.
        $app->setUserState('com_jsports.edit.game.data', null);
    }
        
    
    /**
     * Cancels a Game data-entry edit, clearing the session edit state and
     * redirecting back to the team's schedule page.
     *
     * @param   mixed  $key  Unused; present only for signature compatibility.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function cancel($key = null) : void 
    {
        
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $input = $this->input;
        $requestData = $input->get('jform', [], 'array'); // pulls from request (post/get)
        $teamid = (int) ($requestData['teamid'] ?? $input->getInt('teamid'));
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.game.data', null);
        
        // Redirect to team schedule.
        $this->setMessage(Text::_('COM_JSPORTS_OPERATION_CANCELLED'),'success');
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $teamid, false));
//         $this->setRedirect(Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $contextid, false));
    }
}
