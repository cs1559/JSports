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
use FP4P\Component\JSports\Site\Objects\Application;

use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use Joomla\CMS\Component\ComponentHelper;

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
    
    public function display($cachable = false, $urlparams = [])
    {
        
        $app            = $this->app;
//         $user           = $this->app->getIdentity();
        
        parent::display($cachable = false, $urlparams = []);
    }
    
    
    /**
     * This function will SAVE the game item.
     *
     * @return  void|boolean
     *
     * @since   1.6
     * @throws  \Exception
     */
    
    
    public function save2($key = null, $urlVar = null)
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
     * Function to support a CANCEL operation from the Game data entry screen.  The redirect goes back to the team schedules page.
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
        //$teamid = $requestData['teamid'];
        $redirectteamid = $requestData['redirectteamid'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.postscore.data', null);
                
        // Redirect to team schedule.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=postscores&teamid=' . $redirectteamid, false));
    }
}
