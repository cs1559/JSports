<?php
/**
 * JSports - Joomla Sports Management Component
 */

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\LogService;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;

class BulletinModel extends AdminModel
{
    
    public $uploadError = false;
    
    public function save($data) {
        
        $app   = Factory::getApplication();
        $input = $app->input;
        
        // Posted form data
        $requestData   = $input->post->get('jform', [], 'array');
        $bulletinTitle = $requestData['title'] ?? '';
        
        // File upload array (jform[afile])
        $files = $input->files->get('jform', [], 'array');
        $result = parent::save($data);
        
        if (!$result) {
            return false;
        }
        
        $bulletinId = $this->getState('bulletin.id');
        
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
        if ($isNew && $bulletinId > 0) {
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
        $item = parent::getItem($pk);

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
        if (empty($record->id) || (int) $record->published !== -2) {
            return false;
        }

        return $this->getCurrentUser()->authorise(
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
