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


use Joomla\Filesystem\Folder;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Filesystem\File;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\BulletinService;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;

/**
 * This model supports methods required to enable users to register for a program from the front-end side of the JSports component.
 *
 * @since  1.6
 */
class BulletinModel extends FormModel
{
    
    public $uploadError = false;
    public $team = null;
    public $teamid = null;
    
    public function getTeam() : ?TeamsTable {
        return $this->team;
    }
    public function save($data) {
    
        $bsvc = new BulletinService();
        $bulletin = $bsvc->getBulletinsTable();
        
        $app   = Factory::getApplication();
        $input = $app->input;
//         $user   = $app->getIdentity();
        $user = UserService::getUser();
        
        // Posted form data
        $requestData   = $input->post->get('jform', [], 'array');
        $bulletinTitle = $requestData['title'] ?? '';
        $isNew = empty($data['id']) || (int) $data['id'] === 0;
        
        
        // File upload array (jform[afile])
        $files = $input->files->get('jform', [], 'array');
        
        /**
         * Business rule - anytime a bulletin is SAVED, reset the approved value
         */
        $data['approved'] = 0;
        $data['published'] = 0;
        $data['updatedby'] = $user->username;
        
        if ($isNew) {
            $data['ownerid'] = $user->id;
        }
        
        $bulletin->bind($data);
        $bulletin->check();
                
//         $bulletin->updatedby = $user->username;
        
        $result = $bulletin->save($data);
        if (!$result) {
            return false;
        }

        /**
         * NOTE:  From beyond this point in the model, return should always be TRUE.  Any future failures 
         * should set the uploaderror flag.
         */
        
        $bulletinId = $this->getState($this->getName() . '.id');
        
        // Fallbacks if needed
        if ($bulletinId <= 0) {
            $bulletinId = (int) ($requestData['id'] ?? 0);
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
            
            $params = ComponentHelper::getParams('com_jsports');
            $maxsize = $params->get('maxuploadsize');
            $maxBytes = $maxsize * 1024;
            
            if (($afile['size'] ?? 0) > $maxBytes ) { // 10 MB example
                $errmsg = "Bulletin {$bulletinId}: Attachment too large";
                LogService::error($errmsg);
                $this->setError($errmsg);
                $this->uploadError = true;
                return true;
            }
            
            /**
             * If the upload process fails, the user should be redirected to the bulletin list and 
             * displayed an error message.  The db record was saved, but the attachment didn't work.
             */
            if (File::upload($src, $dest)) {
                LogService::info("Bulletin " . $bulletinId . ": File " . $safeName . " has been uploaded");
                if (!BulletinService::updateAttachmentFilename($bulletinId, $safeName)) {
                    $errmsg = "Bulletin " . $bulletinId . ": failed to update the filename to " . $safeName . " ";
                    LogService::error($errmsg);
                    $this->setError($errmsg);
                    $this->uploadError = true;
                } else {
                    LogService::info("Bulletin " . $bulletinId . ": Record filename has been updated to " . $safeName . " ");
                }
            } else {
                $errmsg = "Error uploading attachment " . $safeName . " ";
                LogService::error($errmsg . " for bulletin id = " . $bulletinId);
                $this->setError($errmsg);
                $this->uploadError = true;
            }
        
        }
        
        return true;
        
    }
    
    public function getItem($pk = null)
    {
    $pk = $pk ?: (int) $this->getState('item.id');           // or bulletin.id if you prefer

    if (!$pk) {
        $pk = Factory::getApplication()->input->getInt('id');
    }


    $teamid = (int) $this->getState('bulletin.teamid')
        ?: Factory::getApplication()->input->getInt('teamid');


    $svc  = new BulletinService();
    $item = $svc->getItem($pk);
        
        if (!$item) {
            return null; // or return false; consistent with your calling code
        }
        $tsvc = new TeamService();
        if ((int) $item->teamid > 0) {
            $this->team = $tsvc->getItem($item->teamid);
        } else {
            $this->team = $tsvc->getItem($teamid);
            $item->teamid = $teamid;
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
//         $user = Factory::getApplication()->getIdentity();
        $user = UserService::getUser();
        
        if (empty($record->id) || (int) $record->published !== -2) {
            return false;
        }
        
        return $user->authorise(
            'core.delete',
            'com_jsports.bulletin.' . (int) $record->id
            );
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
        $form = $this->loadForm('com_jsports.bulletin', 'bulletin', ['control' => 'jform', 'load_data' => true]);
        
        if (empty($form))
        {
            return false;
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }
        $game = $this->getItem($this->getState('bulletin.id'));
        //         $game = $this->getItem();
        return $form;
    }
    
    protected function loadFormData()
    {
	    $data = Factory::getApplication()->getUserState(
        	'com_jsports_form.bulletin.data',
       	 []
    	);

    	if (empty($data)) {
        	$data = $this->getItem();
    	}

    	$this->preprocessData('com_jsports.bulletin', $data);

    	return $data;
    }
    
    
    protected function populateState() {
        
        parent::populateState();

        /** @var SiteApplication $app */
        $app = Factory::getContainer()->get(SiteApplication::class);
        $this->setState('bulletin.id', $app->getInput()->getInt('id'));
        $this->setState('bulletin.teamid', $app->getInput()->getInt('teamid'));        
    }
    
}
