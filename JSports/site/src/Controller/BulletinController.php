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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Input\Input;
use FP4P\Component\JSports\Site\Objects\Application;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Model\BulletinModel;
use FP4P\Component\JSports\Site\Services\UserService;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller object for an individiaul REGISTRATION entry
 *
 * @since  1.6
 */
class BulletinController extends BaseController
{
    
    /* @var string $redirectTeams */
    const REDIRECTBULLETINS_URL = "index.php?option=com_jsports&view=bulletins&teamid=";
    
    /**
     * Method to save a registration data.
     *
     * @return  void|boolean
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function save()
    {
        // Check for request forgeries.
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $jsapp = Myapp::getInstance();
        
        $app    = $this->app;
        $input = $app->input;
//         $user   = $this->app->getIdentity();
        $user = UserService::getUser();

        // Posted form data
        $requestData   = $input->post->get('jform', [], 'array');
        $teamid = (int) ($requestData['teamid'] ?? 0);
 
        //check user session
        if ($user->guest) {
            $app->enqueueMessage(Text::_('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL . $teamid, false));
            return false;
        }
        
        // 4. Persistence
        /* @var \FP4P\Component\JSports\Site\Model\BulletinModel $model */
        $model  = $this->getModel();
        $result = $model->save($requestData);


        // File upload array (jform[afile])
//         $files = $input->files->get('jform', [], 'array');
        
        // Determine if "new" based on incoming id (before save)
//         $incomingId = (int) ($requestData['id'] ?? $input->getInt('id'));
        
        if (!$result) {
            $msg = "An error occurred saving the bulletin.";
            if ($model->uploadError) {
                $msg = $msg . " File upload failed - check logs.";
            }
            LogService::error($msg);
            $app->enqueueMessage($msg, "error");
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletin&layout=edit&id=' . $bulletinid . '&teamid=' . $teamid, false));
//             $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL . $teamid,false));
            return false;
        } else {
            $msg = "Bulletin has been saved.";
            
            $data = (object)[
                'title' => $requestData['title'],
                'content' => $requestData['content'],
                'updatedby' => $requestData['updatedby'],
            ];
            
            $jsapp->triggerEvent('onAfterBulletinSave', ['data' => $data]);
            
            if ($model->uploadError) {
                $msg = $msg . " File upload failed - check logs.";
                $app->enqueueMessage($msg, "warning");
                $app->enqueueMessage($model->getError(), 'error');
                LogService::error($msg);
                $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL . $teamid, false));
                return false;
            } else {
                $app->enqueueMessage($msg, "message");
                LogService::info($msg);
                $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL. $teamid,false));
                return true;
            }
           
        }
        
        return true;
        
    }
    
    public function delete() {
        
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $app   = $this->app;
        $input = $app->input;
        
        $id    = $input->getInt('id');
        $teamId = $input->getInt('teamid');
        
//         $user = $app->getIdentity();
        $user = UserService::getUser();
        if ($user->guest) {
            $app->enqueueMessage(Text::_('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL . $teamId, false));
            return false;
        }
        
        if ($id <= 0) {
            $app->enqueueMessage('Invalid ID value provided - Bulletin DELETE failed', 'error');
            $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL . $teamId, false));
            return false;
        }
        
        try {
            $item = (new BulletinService())->getItem($id);
            BulletinService::delete($id);
            
            $app->enqueueMessage("Bulletin '{$item->title}' was successfully deleted", 'message');
            $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL. (int) $item->teamid, false));
            return true;
            
        } catch (\Exception $e) {
            LogService::error('Bulletin delete failed: ' . $e->getMessage());
            $app->enqueueMessage('Delete failed: ' . $e->getMessage(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $teamId, false));
            return false;
        }
    }
    
    public function deleteAttachment($key = null, $urlVar = null) {

        // Check for request forgeries.
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');

        $app   = $this->app;
//         $user = $app->getIdentity();
        $user = UserService::getUser();
        
        /* If this funciton is called and the user is a guest, redirect them to the postings view */
        if ($user->guest) {
            $app->enqueueMessage(Text::_('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=postings', false));
            return false;
        }
        
        $jinput = Factory::getApplication()->input;
        $bulletinid = $jinput->getInt('id');
        
        if (BulletinService::deleteAttachmentFolder($bulletinid)) {
            Factory::getApplication()->enqueueMessage("Attachment has been deleted", 'message');
            LogService::info("Attachment folder for Bulletin ID " . $bulletinid . " has been deleted");
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletin&layout=edit&id=' . $bulletinid, false));
        } else {
            LogService::error("Something happened when trying to remove folder for Bulletin ID " . $bulletinid . " ");
            Factory::getApplication()->enqueueMessage("Something happened when attempting to remove the attachment folder", 'warning');

        }
        
    }
    
    /**
     * Method to cancel an edit.
     *
     * @return  void
     *
     */
    public function cancel()
    {
        // Check for request forgeries.
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        $app    = $this->app;
        // Get the team id.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
        $teamid = $requestData['teamid'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.bulletin', null);
        
        // Redirect to user profile.
        $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL . $teamid, false));
    }

}
