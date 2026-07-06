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

/**
 * REVISION HISTORY:
 * 2025-01-15  Added log messages for SUCCESS/FAILURE of the upload process.  Also added logic
 *             to address potential issue of the GD library isn't enabled on the PHP instance. It
 *             was triggering an error on invoking the 'imagecreatetruecolor' function. 
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;
use Joomla\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\LogService;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

 
    /**
     * Handles a team logo upload: validates the posted file, delegates
     * processing (resize/crop to a square PNG) to LogoUploadService, saves it
     * to /media/com_jsports/images/logos/{prefix}{teamid}/, and records the
     * resulting filename against the team record.
     *
     * @return  boolean  True if the logo was processed and the team record
     *                   updated successfully, false on any validation or
     *                   processing failure.
     *
     * @since   1.6
     */
class LogouploadController extends BaseController
{
    
 
    /**
     * Method to save/upload a logo image.
     *
     * @return  void|boolean
     *
     * @since   1.6
     * @throws  \Exception
     */
    public function save()
    {
        
            $this->checkToken();
            
            $params = ComponentHelper::getParams('com_jsports');
            $prefix = (string) $params->get('logodir_prefix', '');
            if (trim($prefix) === '') {
                $prefix = 'Teamid-';
            }
            
            $size = 175;
            
            $app = $this->app;
            
            $requestData = $app->getInput()->post->get('jform', [], 'array');
            $file = $app->getInput()->files->get('jform', null, 'raw');
            
            $teamid = (int) ($requestData['id'] ?? 0);
            if ($teamid < 1) {
                $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_FAILED'), 'error');
                $this->setRedirect(Route::_('index.php?option=com_jsports&view=teams', false));
                return false;
            }
            
            $logofile = $file['uploadfile'] ?? null;
            if (!$logofile || empty($logofile['tmp_name'])) {
                $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_FAILED'), 'error');
                LogService::error("Logo upload missing file payload. team={$teamid}");
                $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
                return false;
            }
            
            // Build destination dir: /media/com_jsports/images/logos/Teamid-123/
            $relative = Folder::makeSafe('/media/com_jsports/images/logos/' . $prefix . $teamid);
            $destDir = JPATH_SITE . $relative;
            
            // Process + save
            $logoSvc = new \FP4P\Component\JSports\Site\Services\LogoUploadService();
            
            // You can choose a stable name. Examples:
            //  - 'logo' (always overwrites)
            //  - 'team_' . $teamid . '_' . date('Ymd_His') (versioned)
            $result = $logoSvc->processAndSaveSquarePng($logofile, $destDir, $size, 'logo');
            
            if (!$result['success']) {
                $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_FAILED') . ' ' . ($result['error'] ?? ''), 'error');
                LogService::error("Team logo save failed. team={$teamid}. " . ($result['error'] ?? 'Unknown error'));
                $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
                return false;
            }
            
            $savedFilename = $result['filename']; // e.g. logo.png
            
            // Update DB with what we actually saved
            $teamSvc = new TeamService();
            $rc = $teamSvc->updateTeamLogoFilename($teamid, $savedFilename);
            
            if ($rc) {
                $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_SUCCESS'));
                LogService::info("Team Logo updated. team={$teamid}, file={$savedFilename}");
            } else {
                $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_FAILED'), 'warning');
                LogService::error("Logo file saved but DB update failed. team={$teamid}, file={$savedFilename}");
            }
            
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
            return true;
               
    }
    
    
    /**
     * Method to cancel an edit.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function cancel() : void
    {
        
        // Check for request forgeries.
        $this->checkToken();
        
        $app    = $this->app;
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
  
        $teamid = (int) ($requestData['teamid'] ?? $requestData['id'] ?? 0);
        $this->app->setUserState('com_jsports_form.logoupload.data', null);
        
        // Redirect to user profile.
        $this->setMessage(Text::_('COM_JSPORTS_OPERATION_CANCELLED'), 'success');
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
    }
    
}
