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


namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;


use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\MVC\Model\FormModel;
use FP4P\Component\JSports\Site\Objects\Application;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\File;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;

/**
 * This model supports methods required to enable users to register for a program from the front-end side of the JSports component.
 *
 * @since  1.6
 */
class BulletinModel extends FormModel
{
    
    public $uploadError = false;
    public $team = null;
    
    public function save($data) {
    
        $bsvc = new BulletinService();
        $bulletin = $bsvc->getBulletinsTable();
        
        $app   = Factory::getApplication();
        $user   = $app->getIdentity();
        $input = $app->input;
        
        // Posted form data
        $requestData   = $input->post->get('jform', [], 'array');
        $bulletinTitle = $requestData['title'] ?? '';
        
        // File upload array (jform[afile])
        $files = $input->files->get('jform', [], 'array');
        
        $bulletin->bind($data);
        $bulletin->check();
                
        $bulletin->updatedby = $user->username;
        
        $result = $bulletin->save($data);
        
        if (!$result) {
            return false;
        }
        
        $bulletinId = $this->getState($this->getName() . '.id');
        
        // Fallbacks if needed
        if ($bulletinId <= 0) {
            // Try reading from jform (often present post-save)
            $bulletinId = (int) ($requestData['id'] ?? 0);
        }
        if ($bulletinId <= 0) {
            // Try request id
            $bulletinId = (int) $input->getInt('id');
        }
        
        // Log create
        $isNew = empty($data['id']) || (int) $data['id'] === 0;
        
        if ($isNew) {
            LogService::info("Bulletin created - {$bulletinTitle} - ID: {$bulletinId}");
        }
        
        // Handle attachment upload
        $afile = $files['afile'] ?? null;
        
        if (!empty($afile['name']) && !empty($afile['tmp_name']) && $bulletinId > 0) {
            $filepath = JSHelper::getBulletinFilePath($bulletinId);
            
            if (!Folder::exists($filepath)) {
                Folder::create($filepath);
            }
            
            $safeName = File::makeSafe($afile['name']);
            $src      = $afile['tmp_name'];
            $dest     = $filepath . $safeName;
            
            if (File::upload($src, $dest)) {
                LogService::info("Bulletin " . $bulletinId . ": File " . $safeName . " has been uploaded");
                if (!BulletinService::updateAttachmentFilename($bulletinId, $safeName)) {
                    LogService::error("Bulletin " . $bulletinId . ": failed to update the filename to " . $safeName . " ");
                    $this->uploadError = true;
                } else {
                    LogService::info("Bulletin " . $bulletinId . ": Record filename has been updated to " . $safeName . " ");
                }
            } else {
                LogService::error("Error uploading attachment " . $safeName . " for bulletin " . $bulletinId);
                $this->uploadError = true;
            }
        }
        
        return $result;
        
    }
    
    public function getItem($pk = null)
    {
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new BulletinService();
        $item = $svc->getItem($id);
        
        
        if ((int) $item->teamid > 0) {
            $tsvc = new TeamService();
            $pk = (int) $item->teamid;
            $this->team = $tsvc->getItem($pk);
        }

        $item->hasAttachment = false;
        
        if ($item) {
            if (!empty($item->attachment)) {
                $item->hasAttachment = true;
                $item->attachmentUrl = JSHelper::getBulletinAttachmentURL($item->id, $item->attachment);
            } else {
                $item->hasAttachment = false;
                $item->attachmentUrl = null;
            }
        }
        
        return $item;
    }
    
    /**
     * Permissions: only delete if trashed (-2) and user is authorized.
     */
    protected function canDelete($record)
    {
        $user = Factory::getApplication()->getIdentity();
        
        if (empty($record->id) || (int) $record->published !== -2) {
            return false;
        }
        
        return $user->authorise(
            'core.delete',
            'com_jsports.bulletin.' . (int) $record->id
            );
    }
    
    protected function canEditState($record)
    {
        return parent::canEditState($record);
    }
    
    /**
     * Joomla 4/5 namespaced table resolution.
     *
     * Requires:
     *  - class FP4P\Component\JSports\Administrator\Table\BulletinTable
     *  - file administrator/components/com_jsports/src/Table/BulletinsTable.php
     */
    public function getTable($name = 'Bulletins', $prefix = 'Table', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }
    
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_jsports.bulletin',
            'bulletin',
            ['control' => 'jform', 'load_data' => $loadData]
            );
        
        return $form ?: false;
    }
    
    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_jsports.edit.bulletin.data', []);
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        $this->preprocessData('com_jsports.bulletin', $data);
        
        return $data;
    }
    
}