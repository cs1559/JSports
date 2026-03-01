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
use FP4P\Component\JSports\Site\Services\SponsorService;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;

class SponsorModel extends AdminModel
{

    /** @var array<int, \stdClass> */
    protected $sponsorships;
    protected $assets;
    
    /**
     */
    public function getTable($name = 'Sponsors', $prefix = 'Table', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_jsports.sponsor',
            'sponsor',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        return $form ?: false;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_jsports.edit.sponsor.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_jsports.sponsor', $data);

        return $data;
    }
    

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        
        // Always default to empty array (consistent return type)
        $this->sponsorships = [];
        $this->assets = [];
               
    //    if (!empty($item) && !empty($item->id)) {
            $this->sponsorships = SponsorService::getSponsorships((int) $item->id);
            $this->assets = SponsorService::getAssets((int) $item->id);
      //  }
            
        return $item;

    }
    
    /**
     * @return array<int, \stdClass>|array
     */
    public function getSponsorships(): array
    {
        return $this->sponsorships ?? [];
    }
    
    public function getAssets(): array
    {
        return $this->assets ?? [];
    }
    
    public function save($data) {
        
        $app   = Factory::getApplication();
        $input = $app->input;
        $user = UserService::getUser();
        
        // Posted form data
        $requestData   = $input->post->get('jform', [], 'array');
//         $bulletinTitle = $requestData['title'] ?? '';
        
        // File upload array (jform[afile])
        $files = $input->files->get('jform', [], 'array');
        
        $isNew = empty($data['id']) || (int) $data['id'] === 0;
        
//         if ($isNew) {
//             $data['ownerid'] = $user->id;
//         }
        
        $result = parent::save($data);
        
        if (!$result) {
            return false;
        }
        
        $sponsorid = $this->getState($this->getName() . '.id');
        
        // Fallbacks if needed
        if ($sponsorid <= 0) {
            // Try reading from jform (often present post-save)
            $sponsorid = (int) ($requestData['id'] ?? 0);
        }
        if ($sponsorid <= 0) {
            // Try request id
            $sponsorid = (int) $input->getInt('id');
        }
        
//         if ($isNew) {
//             LogService::info("Bulletin created - {$bulletinTitle} - ID: {$bulletinId}");
//         }
        
        // Handle attachment upload
        $afile = $files['afile'] ?? null;
        
        if (!empty($afile['name']) && !empty($afile['tmp_name']) && $sponsorid > 0) {
            $filepath = SponsorHelper::getLogoFolder($sponsorid);
            
            if (!Folder::exists($filepath)) {
                Folder::create($filepath);
            }
            
            $safeName = File::makeSafe($afile['name']);
            $src      = $afile['tmp_name'];
            $dest     = $filepath . $safeName;
            
            if (File::upload($src, $dest)) {
//                 LogService::info("Bulletin " . $bulletinId . ": File " . $safeName . " has been uploaded");
                if (!SponsorService::updateLogoFilename($sponsorid, $safeName)) {
//                     LogService::error("Bulletin " . $bulletinId . ": failed to update the filename to " . $safeName . " ");
                    $this->uploadError = true;
                } else {
//                     LogService::info("Bulletin " . $bulletinId . ": Record filename has been updated to " . $safeName . " ");
                }
            } else {
//                 LogService::error("Error uploading attachment " . $safeName . " for bulletin " . $bulletinId);
                $this->uploadError = true;
            }
        }
        
        return $result;
        
    }
    
}
