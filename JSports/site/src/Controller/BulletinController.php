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
use Joomla\Input\Input;
use FP4P\Component\JSports\Site\Objects\Application;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Filesystem\Folder;
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
     * Saves a bulletin post (create or update) from posted 'jform' data,
     * including any attached file, and fires the onAfterBulletinSave event
     * on success.
     *
     * NOTE: on the failure path this method references $bulletinid when
     * building the redirect route, but that variable is never assigned
     * in this method — it will trigger an "undefined variable" warning.
     * Consider deriving it from $requestData['id'] the same way $teamid
     * is derived, above.
     *
     * @return  boolean  True if the bulletin (and any attachment) saved
     *                   successfully, false otherwise.
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function save() : bool 
    {
        // Check for request forgeries.
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $jsapp = Myapp::getInstance();
        
        $app    = $this->app;
        $input = $app->getInput();
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
            $app->enqueueMessage($model->getError(), 'error');
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
                'updatedby' => $user->username,
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
    
    /**
     * Deletes a bulletin post: verifies the user session, removes the
     * attachment folder if one exists, then deletes the bulletin record.
     *
     * @return  boolean  True on success, false if validation failed or the
     *                   delete operation raised an exception.
     *
     * @since   1.6
     */
    public function delete() : bool {
        
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $app   = $this->app;
        $input = $app->getInput();
        
        $id    = $input->getInt('id');
        $teamId = $input->getInt('teamid');
        
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
        
        $filepath = BulletinService::getBulletinFilePath($id);
        $bulletinId = $id;
        if (is_dir($filepath)) {
            if (BulletinService::deleteAttachmentFolder($id)) {
                echo "folder deleted";
                Factory::getApplication()->enqueueMessage("Attachment folder deleted for ID " . $bulletinId, 'message');
                LogService::info("Attachment folder for Bulletin ID " . $bulletinId . " has been deleted");
            } else {
                LogService::error("Failed removing attachment folder for Bulletin ID " . $bulletinId);
                Factory::getApplication()->enqueueMessage("Could not remove attachment folder for ID " . $bulletinId , 'warning');
                $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $teamId, false));
            }
        }
        
        if (is_dir($filepath)) {
            LogService::error("Bulletin attachment folder still exists when it should have been deleted" . $bulletinId);
            Factory::getApplication()->enqueueMessage("Cant delete bulletin - attachment still exists (" . $bulletinId . ")", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $teamId, false));
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

    /**
     * Deletes the attachment folder for a bulletin (identified by the 'id'
     * request parameter) without deleting the bulletin record itself.
     *
     * @param   mixed  $key     Unused.
     * @param   mixed  $urlVar  Unused.
     *
     * @return  boolean|void  False if the user session is invalid; otherwise
     *                        no explicit return value (redirects and enqueues
     *                        a status message either way).
     *
     * @since   1.6
     */
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
    public function cancel() : void 
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

    
    /**
     * "Bumps" a bulletin post (e.g. refreshes its sort/date so it resurfaces
     * near the top of the list) after verifying the user session and id.
     *
     * @return  boolean|void  False if validation failed or the bump raised an
     *                        exception; otherwise no explicit return value on
     *                        the "bump did not succeed" path (see NOTE below).
     *
     * NOTE: when BulletinService::bump() returns false, this method enqueues
     * a message and redirects but does not return a value — only the true
     * and exception paths return explicitly. Consider adding `return false;`
     * there for consistency.
     *
     * @since   1.6
     */
    public function bump() {
        
        $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
        $app   = $this->app;
        $input = $app->getInput();
        
        $id    = $input->getInt('id');
        $teamId = $input->getInt('teamid');
        
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
            
            if (BulletinService::bump($id)) {
                $app->enqueueMessage("Bulletin '{$item->title}' was successfully bumped/updated", 'message');
                $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL. (int) $item->teamid, false));
            return true;
            } else {
                $app->enqueueMessage("Bump of  '{$item->title}' was NOT successful", 'message');
                $this->setRedirect(Route::_(self::REDIRECTBULLETINS_URL. (int) $item->teamid, false));
            }
            
        } catch (\Exception $e) {
            LogService::error('Bulletin delete failed: ' . $e->getMessage());
            $app->enqueueMessage('Delete failed: ' . $e->getMessage(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $teamId, false));
            return false;
        }
    }
    
    
    
}
