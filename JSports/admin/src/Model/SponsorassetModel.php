<?php
/**
 * JSports - Joomla Sports Management Component
 */
namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die();

use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Services\UserService;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\MVC\Model\AdminModel;

class SponsorassetModel extends AdminModel
{

    /** @var array<int, \stdClass> */
    // protected $sponsorships;

    /**
     */
    public function getTable($name = 'Sponsorassets', $prefix = 'Table', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_jsports.sponsorasset', 'sponsorasset', [
            'control' => 'jform',
            'load_data' => $loadData
        ]);

        return $form ?: false;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_jsports.edit.sponsorasset.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }
        $id = is_array($data) ? ($data['id'] ?? 0) : ($data->id ?? 0);

        $isNew = empty($id);

        if ($isNew) {
            $sponsorid = (int) $app->input->getInt('sponsorid', (int) $app->getUserState('com_jsports.edit.sponsorasset.sponsorid', 0));

            // $data might be an object or array depending on your code path
            if (is_array($data)) {
                $data['sponsorid'] = $data['sponsorid'] ?? $sponsorid;
            } else {
                $data->sponsorid = $data->sponsorid ?? $sponsorid;
            }
        }

        $this->preprocessData('com_jsports.sponsorasset', $data);

        return $data;
    }

    public function save($data)
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $user = UserService::getUser();

        // Posted form data
        $requestData = $input->post->get('jform', [], 'array');
        // $bulletinTitle = $requestData['title'] ?? '';

        // File upload array (jform[afile])
        $files = $input->files->get('jform', [], 'array');

        $isNew = empty($data['id']) || (int) $data['id'] === 0;

        $result = parent::save($data);

        if (! $result) {
            return false;
        }

        $data['id'] = (int) $this->getState($this->getName() . '.id');

        $sponsorid = $this->getState($this->getName() . '.sponsorid');

        // // Fallbacks if needed
        if ($sponsorid <= 0) {
            // Try reading from jform (often present post-save)
            $sponsorid = (int) ($requestData['sponsorid'] ?? 0);
        }
        if ($sponsorid <= 0) {
            // Try request id
            $sponsorid = (int) $input->getInt('sponsorid');
        }

        // if ($isNew) {
        // LogService::info("Bulletin created - {$bulletinTitle} - ID: {$bulletinId}");
        // }

        // Handle attachment upload
        $afile = $files['filename'] ?? null;
        // $afile = $files['afile'] ?? null;

        if (! empty($afile['name']) && ! empty($afile['tmp_name']) && $sponsorid > 0) {
            $filepath = SponsorHelper::getAssetFolder($sponsorid);

            if (! Folder::exists($filepath)) {
                Folder::create($filepath);
            }

            $safeName = File::makeSafe($afile['name']);
            $src = $afile['tmp_name'];
            $dest = $filepath . $safeName;

            $tmpPath = $afile['tmp_name'];
            $imageInfo = getimagesize($tmpPath);
            if ($imageInfo !== false) {
                echo "calc image size";
                $data['width']  = $imageInfo[0];
                $data['height'] = $imageInfo[1];
                $data['mimetype']   = $imageInfo['mime'];
            }
            
            if (File::upload($src, $dest)) {

                $data['filename'] = $safeName;
                $data['filesize'] = $afile['size'];
                // LogService::info("Bulletin " . $bulletinId . ": File " . $safeName . " has been uploaded");
                // if (!SponsorService::updateLogoFilename($sponsorid, $safeName)) {
                // // LogService::error("Bulletin " . $bulletinId . ": failed to update the filename to " . $safeName . " ");
                // $this->uploadError = true;
                // } else {
                // // LogService::info("Bulletin " . $bulletinId . ": Record filename has been updated to " . $safeName . " ");
                // }
            } else {
                // LogService::error("Error uploading attachment " . $safeName . " for bulletin " . $bulletinId);
                $this->uploadError = true;
            }
        }

        return parent::save($data);
    }
}
