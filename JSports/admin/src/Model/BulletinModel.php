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
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\LogService;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\Database\ParameterType;
use FP4P\Component\JSports\Site\Services\UserService;

class BulletinModel extends AdminModel
{
    
    public $uploadError = false;
    
    public function save($data) {
        
        $app   = Factory::getApplication();
        $input = $app->input;
        $user = UserService::getUser();
        
        // Posted form data
        $requestData   = $input->post->get('jform', [], 'array');
        $bulletinTitle = $requestData['title'] ?? '';
        
        // File upload array (jform[afile])
        $files = $input->files->get('jform', [], 'array');
        
        // Set username to whomever is editing/updating the bulletin
        $data['updatedby'] = $user->username;

        $isNew = empty($data['id']) || (int) $data['id'] === 0;
        
        if ($isNew) {
            $data['ownerid'] = $user->id;    
        }
        
        $result = parent::save($data);
        
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
//     protected function canDelete($record)
//     {
//         $user = Factory::getApplication()->getIdentity();
        
//         if (empty($record->id) || (int) $record->published !== -2) {
//             return false;
//         }

//         return $user->authorise(
//             'core.delete',
//             'com_jsports.bulletin.' . (int) $record->id
//         );
//     }

  

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

        public function publish(&$pks, $value = 1): bool
        {
            $pks = array_map('intval', (array) $pks);
            $pks = array_filter($pks);
            
            if (!$pks) {
                return true; // nothing to do
            }
            
            // First do Joomla’s standard publish workflow
            $result = parent::publish($pks, (int) $value);
            
            if (!$result) {
                return false;
            }
            
            // Now update "approved" in addition to "published"
            $db   = $this->getDatabase();
            
            // Choose your desired approved behavior:
            // Option A: approved mirrors published (publish => approved=1, unpublish => approved=0)
            $approvedValue = (int) $value;
            
            // Build update query
            $query = $db->getQuery(true)
            ->update($db->quoteName('#__jsports_bulletins'))
            ->set($db->quoteName('approved') . ' = :approved')
            ->whereIn($db->quoteName('id'), $pks);
            
            $query->bind(':approved', $approvedValue, ParameterType::INTEGER);
            
            // Optional: only set approval metadata when publishing
//             if ((int) $value === 1) {
//                 $query->set($db->quoteName('approved_by') . ' = :approvedBy')
//                 ->set($db->quoteName('approved_date') . ' = :approvedDate');
                
//                 $query->bind(':approvedBy', (int) $user->id, ParameterType::INTEGER);
//                 $query->bind(':approvedDate', $now, ParameterType::STRING);
//             }
            
            $db->setQuery($query);
            $db->execute();
            
            return true;
        }
    
    


}
