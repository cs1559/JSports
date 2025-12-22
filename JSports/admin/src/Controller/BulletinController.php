<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\Database\ParameterType;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\LogService;
use Joomla\CMS\Router\Route;

/**
 * Controller for a BULLETIN
 *
 */
class BulletinController extends FormController
{
    
    
   
    public function save($key = null, $urlVar = null)
    {
            $app   = Factory::getApplication();
            $input = $app->input;
            
            // Posted form data
            $requestData   = $input->post->get('jform', [], 'array');
            $bulletinTitle = $requestData['title'] ?? '';
            
            // File upload array (jform[afile])
            $files = $input->files->get('jform', [], 'array');
            
            // Determine if "new" based on incoming id (before save)
            $incomingId = $input->getInt('id');
            $isNew      = empty($incomingId);
                      
            $model = $this->getModel();
            $result = $model->save($requestData);
            
            if (!$result) {
                $msg = "An error occurred saving the bulletin.";
                if ($model->uploadError) {
                    $msg = $msg . " File upload failed - check logs.";
                }
                LogService::error($msg);
                $app->enqueueMessage($msg, "error");
                $this->setRedirect('index.php?option=com_jsports&view=bulletins');
                return false;
            } else {
                $msg = "Bulletin has been saved.";
                if ($model->uploadError) {
                    $msg = $msg . " File upload failed - check logs.";
                    $app->enqueueMessage($msg, "warning");
                    LogService::error($msg);
                    $this->setRedirect('index.php?option=com_jsports&view=bulletins');
                    return false;
                } else {
                    $app->enqueueMessage($msg, "message");
                    LogService::info($msg);
                    $this->setRedirect('index.php?option=com_jsports&view=bulletins');
                    return true;
                }
                LogService::info($msg);

            }
            // defaul
            $this->setRedirect('index.php?option=com_jsports&view=bulletins');
            return true;
    }
    
   
    public function deleteAttachment($key = null, $urlVar = null) {
        
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
    
    
    public function delete(&$pks) {
     
        $pks = array_map('intval', (array) $pks);
        
        foreach ($pks as $bulletinId) {
            if ($bulletinId > 0) {
                if (BulletinService::deleteAttachmentFolder($bulletinId)) {
                    Factory::getApplication()->enqueueMessage("Attachment folder deleted for ID " . $bulletinId, 'message');
                    LogService::info("Attachment folder for Bulletin ID " . $bulletinId . " has been deleted");
                } else {
                    LogService::error("Failed removing attachment folder for Bulletin ID " . $bulletinId);
                    Factory::getApplication()->enqueueMessage("Could not remove attachment folder for ID " . $bulletinId , 'warning');
                }
            }
        }
        
        return parent::delete($pks);
    }

}