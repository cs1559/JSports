<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Services\UserService;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller for an individual team's profile page: display, save,
 * cancel-edit, and CSV schedule export.
 *
 * @since  1.0.0
 */
class TeamController extends BaseController
{
    /**
     * Displays the requested view; delegates to the parent BaseController.
     *
     * NOTE: like other controllers in this component, the parameters
     * received here are ignored — the call below always passes
     * ($cachable = false, $urlparams = []) rather than forwarding the
     * caller's actual arguments.
     *
     * @param   boolean  $cachable   If true, the view output will be cached
     *                               (currently not actually honored — see NOTE).
     * @param   array    $urlparams  Safe URL parameters (currently not
     *                               actually honored — see NOTE).
     *
     * @return  static  This object to support chaining.
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = [])
    {
        
        $app            = $this->app;
//         $user           = $this->app->getIdentity();
        $user = UserService::getUser();
        
        
        $params = ComponentHelper::getParams('com_jsports');
        $itemid = $params->get('itemid');
        
        parent::display($cachable = false, $urlparams = []);
    }
    
    /**
     * Validates and saves a team profile update from posted 'jform' data.
     * Fires the onAfterProfileOwnerUpdate event if the team's owner changed.
     *
     * @return  boolean|void  False if the user session is invalid, validation
     *                        fails, or the save fails. No explicit return
     *                        value on the success path (redirects and clears
     *                        session state instead).
     *
     * @throws  \Exception  If the model's form cannot be loaded.
     * @since   1.0
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
//         $user   = $this->app->getIdentity();
        $user = UserService::getUser();
        
        
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
        
        $teamid = (int) ($requestData['teamid'] ?? $requestData['id'] ?? 0);
        
        /* Code to prevent further action if user is NOT logged in */
//         $user = Factory::getUser();
        $user = UserService::getUser();
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
        
        LogService::writeArray($data,'TEAMPROFILE');
        
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
     * Cancels a team profile edit, clearing the session edit state and
     * redirecting back to the team's profile page.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function cancel()
    {
        // Check for request forgeries.
        $this->checkToken();
        
        $app    = $this->app;
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
        $teamid = (int) ($requestData['teamid'] ?? $requestData['id'] ?? 0);
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.team.data', null);
        
        // Redirect to user profile.
        $this->setMessage(Text::_('COM_JSPORTS_OPERATION_CANCELLED'), 'success');
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
    }
    
    /**
     * Streams a team's program schedule as a downloadable CSV file.
     * Expects 'teamid' and 'programid' request parameters.
     *
     * @return  void  Never returns normally — always terminates via $app->close().
     *
     * @since   1.0.0
     */
    public function downloadSchedule() {
        $app = Factory::getApplication();
//         $input = $app->input;
        $input = $app->getInput();
        
        $teamid     = $input->getInt("teamid");
        $programid     = $input->getInt("programid");
        
        $games = GameService::getTeamSchedule($teamid, $programid);
        $team = TeamService::getItem($teamid);
        $program = ProgramsService::getItem($programid);
        
        $fnprefix = 'team-schedule';
        $date = date('Y-m-d-s');
        $filename = $fnprefix . '-' . $date . '.csv';
        
        $app->setHeader('Content-Type', 'text/csv; charset=utf-8', true);
        $app->setHeader('Content-disposition', 'attachment; filename="' . $filename . '"', true);
        $app->setHeader('Cache-Control', 'no-cache', true);
        $app->sendHeaders();
        
        echo '"Team: ' . $team->name . '"'. "\n";
        echo '"Program: ' . $program->name . '"'. "\n";
        echo "\n";
        // Send HEADER ROW
        echo "DATE, TIME, GAME, LOCATION, W/L, AWAY SCORE, HOME SCORE, STATUS" . "\n";
        //... send the rows
        foreach ($games as $game) {
            if (!$game->leaguegame) {
                $game->name = $game->name . '**';
            }
            echo '"' . $game->gamedate . '",' .
                '"' .JSHelper::displayGameTime($game->gametime) . '",' .
                '"' .$game->name . '",' .
                '"' .$game->location . '",' .
                '"' .GameService::getWinLoss($teamid,$game) . '",' .
                '"' .$game->awayteamscore . '",' .
                '"' .$game->hometeamscore . '",' .
                '"' .$game->gamestatus . '"' . "\n";
        }
        
        $app->close();
    }
    
}
