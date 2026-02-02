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
use Joomla\CMS\Input\Input;
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
 * Controller object for uploading a team logo
 *
 * @since  1.6
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
        
//         $this->checkToken($this->input->getMethod() == 'GET' ? 'get' : 'post');
        
//         $params = ComponentHelper::getParams('com_jsports');
//         $prefix = $params->get("logodir_prefix");
        
//         $rheight = 175;
//         $rwidth = 175;
        
//     	if (strlen($prefix) < 1) {
//     		$prefix = "Teamid-";
//     	}
                
//         $app    = $this->app;
//         $requestData = $app->getInput()->post->get('jform', [], 'array');
//     	$file = $app->getInput()->files->get('jform', null, 'raw');
        
//     	$logofile = $file['uploadfile'];
    
//         // Do not change the filter type 'raw'. We need this to let files containing PHP code 
//         // to upload. See \JInputFiles::get.
//         //$logofile = $input->files->get('jform[uploadfile]', null, 'raw');
	
//     	$teamid = $requestData['id'];
               
//         // Actual name of the file being uploaded.
//         $filename = File::makeSafe($logofile['name']);
//         $safeFilename = $filename;
                       
//         // Calculate the path to the teams logo.
//         // @TODO Revisit this.  May want to have the folder naming convention (along with f
//         // older) configurable at the component level
//         //$filepath = Folder::makeSafe( '\\media\\com_jsports\\images\\logos\\' . $prefix . $teamid .'');
//         $filepath = Folder::makeSafe( '/media/com_jsports/images/logos/' . $prefix . $teamid .'');

//         $filepath = JPATH_SITE . $filepath;

//         if (!Folder::exists($filepath)) {
//             Folder::create($filepath);
//         }

//         // 02-26-2024 - changed filename delimeter
//         //$filename = $filepath . "\\" . $filename;
//         $origfile = $filename;
//         $filename = $filepath . "/" . $filename;
        
//         // DEfine what file types are allowed to be uploaded.
//         $allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/JPG', 'image/jpg', 'image/pjpeg');
        
//         if (!in_array($logofile['type'], $allowed)) //To check if the file are image file
//         {
//             echo "<script> alert('The file you are trying to upload is not supported.');
// 		window.history.back();</script>\n";
//             exit;
//         }
//         else
//         {
//             $ext = File::getExt($logofile['name']);//Get extension of the file
            
//             switch ($ext)
//             {
//                 case 'jpeg':
//                 case 'pjpeg':
//                 case 'JPG':
//                 case 'jpg':
//                     $src = ImageCreateFromJpeg($logofile['tmp_name']);
//                     break;
                    
//                 case 'png':
//                     $src = ImageCreateFromPng($logofile['tmp_name']);
//                     break;
                    
//                 case 'gif':
//                     $src = ImageCreateFromGif($logofile['tmp_name']);
//                     break;
//                 default:
//                     break;
                    
//             }
            
                        
//             list($width,$height)=getimagesize($logofile['tmp_name']);
//             $newwidth=$rwidth; //600;//set file width to 600
//             $newheight=($height/$width)*$rheight; // 600;//the height are set according to ratio
            
// //             if (function_exists('imagecreatetruecolor')) {
// //                 $tmp=imagecreatetruecolor($newwidth,$newheight);
// //             } else {
// //                 $tmp=imagecreate($newwidth,$newheight);
// //             }
//             $tmp = function_exists('imagecreatetruecolor')
//             ? imagecreatetruecolor($newwidth, $newheight)
//             : imagecreate($newwidth, $newheight);
            
//             // Preserve transparency for PNG/GIF
//             if ($ext === 'png' || $ext === 'gif') {
//                 imagealphablending($tmp, false);
//                 imagesavealpha($tmp, true);
//                 $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
//                 imagefilledrectangle($tmp, 0, 0, $newwidth, $newheight, $transparent);
//             }
            
//             imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            
// //             imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);//resample the image
            
            
//             switch ($ext)
//             {
//                 case 'jpeg':
//                 case 'JPG':
//                 case 'jpg':
//                     $statusupload = imagejpeg($tmp,$filename,5);//upload the image
//                     break;
//                 case 'png':
//                     $statusupload =  imagepng($tmp,$filename,5);//upload the image
//                     break;        
//                 case 'gif':
//                     $statusupload = imagegif($tmp,$filename,100);//upload the image
//                     break;
//                 default:
//                     break;
//             }
            
//             imagedestroy($tmp);
//             imagedestroy($src);


//             // Update the team database record with the filename
//             $svc = new TeamService();
                       
// //             $rc = $svc->updateTeamLogoFilename($teamid, $logofile['name']);
//             $rc = $svc->updateTeamLogoFilename($teamid, $safeFilename);
        
//             if ($rc) {
//                 $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_SUCCESS'));
//                 LogService::info("Team Logo has been updated - " . $origfile);
//             } else {
//                 $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_FAIL'));
//                 LogService::error("Update of team logo has failed - " . $origfile);
//             }

//             // Redirect to the edit screen.
//             $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
            
//             return true;
            
//         }
        
        
        
        
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
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array');
  
        $teamid = (int) ($requestData['teamid'] ?? $requestData['id'] ?? 0);
        $this->app->setUserState('com_jsports_form.logoupload.data', null);
        
        // Redirect to user profile.
        $this->setMessage(Text::_('COM_JSPORTS_OPERATION_CANCELLED'), 'success');
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
    }
    
}
