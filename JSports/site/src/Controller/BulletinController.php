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
        $this->checkToken('post');
        
        $app    = $this->app;
        $input = $app->input;

        $user   = $this->app->getIdentity();
        $userId = (int) $user->get('id');

        // Posted form data
        $requestData   = $input->post->get('jform', [], 'array');
        $teamid = $requestData['teamid'];
 //       $bulletinTitle = $requestData['title'] ?? '';
        
        //check user session
        if ($user->guest) {
            $app->enqueueMessage(Text::_('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
            return false;
        }
        
        $model  = $this->getModel('Bulletin', 'Site');

        // File upload array (jform[afile])
        $files = $input->files->get('jform', [], 'array');
        
        // Determine if "new" based on incoming id (before save)
        $incomingId = (int) ($requestData['id'] ?? $input->getInt('id'));
        $isNew = $incomingId === 0;
        
//        $model = $this->getModel();
        $result = $model->save($requestData);
        
        if (!$result) {
            $msg = "An error occurred saving the bulletin.";
            if ($model->uploadError) {
                $msg = $msg . " File upload failed - check logs.";
            }
            LogService::error($msg);
            $app->enqueueMessage($msg, "error");
            $this->setRedirect('index.php?option=com_jsports&view=bulletins&teamid=' . $teamid);
            return false;
        } else {
            $msg = "Bulletin has been saved.";
            if ($model->uploadError) {
                $msg = $msg . " File upload failed - check logs.";
                $app->enqueueMessage($msg, "warning");
                LogService::error($msg);
                $this->setRedirect('index.php?option=com_jsports&view=bulletins&teamid=' . $teamid);
                return false;
            } else {
                $app->enqueueMessage($msg, "message");
                LogService::info($msg);
                $this->setRedirect('index.php?option=com_jsports&view=bulletins&teamid=' . $teamid);
                return true;
            }
            LogService::info($msg);
            
        }
        
        return true;
        
        // ================================
    }
    
    public function delete() {
        
        $this->checkToken('get'); // or 'post' if your delete is a POST
        
        $app   = $this->app;
        $input = $app->input;
        
        $id    = $input->getInt('id');
        $teamId = $input->getInt('teamid');
        
        $user = $app->getIdentity();
        if ($user->guest) {
            $app->enqueueMessage(Text::_('COM_JSPORTS_INVALID_USERSESSION'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $teamId, false));
            return false;
        }
        
        if ($id <= 0) {
            $app->enqueueMessage('Invalid ID value provided - Bulletin DELETE failed', 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $teamId, false));
            return false;
        }
        
        try {
            $item = (new BulletinService())->getItem($id);
            BulletinService::delete($id);
            
            $app->enqueueMessage("Bulletin '{$item->title}' was successfully deleted", 'message');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . (int) $item->teamid, false));
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
        $this->checkToken();
        
        $jinput = Factory::getApplication()->input;
        $files  = $jinput->files->get('jform', [], 'array');
        $bulletinid = $jinput->getInt('id');
        
        $filepath = JSHelper::getBulletinFilePath($bulletinid);
        
        if (BulletinService::deleteAttachmentFolder($bulletinid)) {
            Factory::getApplication()->enqueueMessage("Attachment has been deleted", 'message');
            LogService::info("Attachment folder for Bulletin ID " . $bulletinid . " has been deleted");
            $this->setRedirect('index.php?option=com_jsports&view=bulletin&layout=edit&id=' . $bulletinid);
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
     * @since   4.0.0
     */
    public function cancel()
    {
        // Check for request forgeries.
        $this->checkToken();
        $app    = $this->app;
        // Get the team id.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
        $teamid = $requestData['teamid'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.bulletin', null);
        
        // Redirect to user profile.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=bulletins&teamid=' . $teamid, false));
    }

}
