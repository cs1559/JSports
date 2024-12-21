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
use Joomla\CMS\Input\Input;
use Joomla\CMS\Factory;

use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller object for an individiaul GOLF entry
 *
 * @since  1.6
 */
class GameController extends FormController
{
    
    public function display($cachable = false, $urlparams = [])
    {
        
//        $params = ComponentHelper::getParams('com_jsports');
//        $itemid = $params->get('itemid');

        parent::display($cachable = false, $urlparams = []);
    }
    
    /**
     * Function to support the DELETION of a game from the team schedule
     * @return boolean
     */
    public function delete() {

        $logger = Myapp::getLogger();
        $app = Factory::getApplication();
        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $lasturl = $_SERVER['HTTP_REFERER'];
        
        /* Code to prevent further action if user is NOT logged in */
        $user = Factory::getUser();
        // Check if the user is logged in
        if ($user->guest) {
            $app->enqueueMessage(Text::sprintf('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $logger->info('Game ID: ' . $id. ' has been DELETED  ' . $item->gamedate . ' ' . $item->name . ' STATUS=' . $item->gamestatus);
            $this->setRedirect(Route::_($lasturl, false));
            return false;
        }
   
        if ($id == 0) {
            $this->setMessage("Invalid ID value provied - Game DELETE failed",'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=dashboard', false));
            return false;
        }
   
        // Get the record to be deleted so you know the team ID and the program ID.
        $svc = new GameService();
        $item = $svc->getItem($id);
        
        $redirectURL = 'index.php?option=com_jsports&view=dashboard';
        $rUrl = 'index.php?option=com_jsports&view=schedules&teamid=' . 
                $item->teamid . '&programid=' . $item->programid;
        if ($item->gamestatus === 'C') {
            $this->setMessage("Completed Game CANNOT be deleted",'info');
            $redirectURL = $rUrl;
        } else {
            
            try {
                $result = GameService::delete($id);
                if ($result) {
                    $logger->info('Game ID: ' . $id. ' has been DELETED  ' . $item->gamedate . ' ' . $item->name . ' STATUS=' . $item->gamestatus);
                    $this->setMessage("Game ITEM was successfully deleted",'info');
                } else {
                    $logger->error('Game ID: ' . $id. ' has NOT been deleted');
                    $this->setMessage("Game was NOT deleted",'info');
                }
                $redirectURL = $rUrl;
                
            } catch (Exception $e) {
                $errors = $item->getErrors();
                $this->setError($errors[0]);
                $app->enqueueMessage($errors[0],'error');
                $redirectURL = 'index.php?option=com_jsports&view=schedules&teamid=' . 
                    $item->teamid   . '&programid=' . $item->programid;
            }
        }
        
        $this->setRedirect(Route::_($redirectURL));
        
    }

    
    /**
     * Function to support the RESET of a game from the team schedule.  This function resets the status to 'S'
     * @return boolean
     */
    public function reset() {
        
        $logger = Myapp::getLogger();
        $app = Factory::getApplication();
        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        if ($id == 0) {
            $this->setMessage("Invalid ID value provied - Game RESET failed",'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=dashboard', false));
            return false;
        }
        
        // Get the record to be deleted so you know the team ID and the program ID.
        $svc = new GameService();
        $item = $svc->getItem($id);
        
        $redirectURL = 'index.php?option=com_jsports&view=dashboard';
        $rUrl = 'index.php?option=com_jsports&view=schedules&teamid=' . $item->teamid . '&programid=' . $item->programid;
            try {
                $result = GameService::reset($id);
                if ($result) {
                    $logger->info('Game ID: ' . $id . ' status has been reset');
                    $this->setMessage("Game status was successfully reset",'info');
                } else {
                    $this->setMessage("Game status was NOT reset",'info');
                }
                $redirectURL = $rUrl;
                
            } catch (Exception $e) {
                $errors = $item->getErrors();
                $this->setError($errors[0]);
                $app->enqueueMessage($errors[0],'error');
                $redirectURL = 'index.php?option=com_jsports&view=schedules&teamid=' .
                    $item->teamid   . '&programid=' . $item->programid;
            }
        
        $this->setRedirect(Route::_($redirectURL));
        
    }
    
    
    
    
    /**
     * This function will SAVE the game item.
     *
     * @return  void|boolean
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function save($key = null, $urlVar = null)
    {
                
        // Check for request forgeries.
        $this->checkToken();
        
        /*
         * @todo Need to validate the session has been authenticated
         */

        $app    = $this->app;
        $model  = $this->getModel('Game', 'Site');

        
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');

        $gameid = $requestData['id'];
        $teamid = $requestData['teamid'];
        
        /* Code to prevent further action if user is NOT logged in */
        $user = Factory::getUser();
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
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=game&layout=edit&id=' .$gameid, false));
            
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
                Route::_('index.php?option=com_jsports&view=game&layout=edit&id=' . $data['teamid']
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
     * Function to support a CANCEL operation from the Game data entry screen.  The redirect
     * goes back to the team schedules page.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function cancel($key = null)
    {
        // Check for request forgeries.
        $this->checkToken();

        $app    = $this->app;
        // Get the team id.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
        $teamid = $requestData['teamid'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.game.data', null);
        
        // Redirect to team schedule.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $teamid, false));
    }
}
