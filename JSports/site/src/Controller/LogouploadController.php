<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use FP4P\Component\JSports\Site\Services\TeamService;


// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller object for an individiaul REGISTRATION entry
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
        $params = ComponentHelper::getParams('com_jsports');
        $prefix = $params->get("logodir_prefix");
    
        
    	if (strlen($prefix) < 1) {
    		$prefix = "Teamid-";
    	} 
        
        $resize = true;
        $rheight = 175;
        $rwidth = 175;
        
        // Check for request forgeries.
        $this->checkToken();
        
        $app    = $this->app;
        // Get the uploaded file information.
        $input    = Factory::getApplication()->getInput();

        $requestData = $app->getInput()->post->get('jform', [], 'array');
    	$file = $app->getInput()->files->get('jform', null, 'raw');
        
    	$logofile = $file['uploadfile'];
    
        // Do not change the filter type 'raw'. We need this to let files containing PHP code to upload. See \JInputFiles::get.
    	//$logofile = $input->files->get('jform[uploadfile]', null, 'raw');
	
    	$teamid = $requestData['id'];
               
        // Actual name of the file being uploaded.
        $filename = File::makeSafe($logofile['name']);
                       
        // Calculate the path to the teams logo.  
        // @TODO Revisit this.  May want to have the folder naming convention (along with folder) configurable at the component level
        //$filepath = Folder::makeSafe( '\\media\\com_jsports\\images\\logos\\' . $prefix . $teamid .'');
        $filepath = Folder::makeSafe( '/media/com_jsports/images/logos/' . $prefix . $teamid .'');

        $filepath = JPATH_SITE . $filepath;

        if (!Folder::exists($filepath)) {
            Folder::create($filepath);
        }

        // 02-26-2024 - changed filename delimeter 
        //$filename = $filepath . "\\" . $filename;
        $filename = $filepath . "/" . $filename;
        
        // DEfine what file types are allowed to be uploaded.
        $allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/JPG', 'image/jpg', 'image/pjpeg');
        
        if (!in_array($logofile['type'], $allowed)) //To check if the file are image file
        {
            echo "<script> alert('The file you are trying to upload is not supported.');
		window.history.back();</script>\n";
            exit;
        }
        else
        {
            $ext = File::getExt($logofile['name']);//Get extension of the file
            
            switch ($ext)
            {
                case 'jpeg':
                case 'pjpeg':
                case 'JPG':
                case 'jpg':
                    $src = ImageCreateFromJpeg($logofile['tmp_name']);
                    break;
                    
                case 'png':
                    $src = ImageCreateFromPng($logofile['tmp_name']);
                    break;
                    
                case 'gif':
                    $src = ImageCreateFromGif($logofile['tmp_name']);
                    break;
                    
            }
            
                        
            list($width,$height)=getimagesize($logofile['tmp_name']);
            $newwidth=$rwidth; //600;//set file width to 600
            $newheight=($height/$width)*$rheight; // 600;//the height are set according to ratio
            $tmp=imagecreatetruecolor($newwidth,$newheight);
            imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);//resample the image
            
            
            switch ($ext)
            {
                case 'jpeg':
                case 'JPG':
                case 'jpg':
                    echo "here i am";
                    $statusupload = imagejpeg($tmp,$filename,5);//upload the image
                    break;
                    
                case 'png':
                    $statusupload =  imagepng($tmp,$filename,5);//upload the image
                    break;
                    
                case 'gif':
                    $statusupload = imagegif($tmp,$filepath,100);//upload the image
                    break;
                    
            }
            
            imagedestroy($tmp);


            // Update the team database record with the filename
            $svc = new TeamService();
            
            
            $rc = $svc->updateTeamLogoFilename($teamid, $logofile['name']);
        
            if ($rc) {
                $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_SUCCESS'));
            } else {
                $this->setMessage(Text::_('COM_JSPORTS_TEAMLOGO_SAVE_FAIL'));
            }          
            
            // Redirect to the edit screen.
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
            
            return true;
            
        }
        
        
        
        
        
        
        // =========================================================================
//         $model  = $this->getModel('Team', 'Site');
//         $user   = $this->app->getIdentity();
        
        
//         // Get the user data.
//         $requestData = $app->getInput()->post->get('jform', [], 'array');
        
//         $teamid = $requestData['id'];
        
//         // Validate the posted data.
//         $form = $model->getForm();
        
//         if (!$form) {
//             throw new \Exception($model->getError(), 500);
//         }
        
//         // Send an object which can be modified through the plugin event
//         //         $objData = (object) $requestData;
//         //         $app->triggerEvent(
//         //             'onContentNormaliseRequestData',
//         //             ['com_users.user', $objData, $form]
//         //             );
//         //         $requestData = (array) $objData;
        
//         // Validate the posted data.
//         $data = $model->validate($form, $requestData);
        
//         // Check for errors.
//         if ($data === false) {
//             // Get the validation messages.
//             $errors = $model->getErrors();
            
//             // Push up to three validation messages out to the user.
//             for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
//                 if ($errors[$i] instanceof \Exception) {
//                     $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
//                 } else {
//                     $app->enqueueMessage($errors[$i], 'warning');
//                 }
//             }
            
//             // Save the data in the session.
//             $app->setUserState('com_jsports.edit.team.data', $requestData);
            
//             // Redirect back to the edit screen.
//             $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&layout=edit&id=' .$teamid, false));
            
//             return false;
//         }
        
//         // Attempt to save the data.
//         $return = $model->save($data);
        
//         // Check for errors.
//         if ($return === false) {
//             // Save the data in the session.
//             $app->setUserState('com_jsports.edit.team.data', $data);
            
//             // Redirect back to the edit screen.
//             $this->setMessage(Text::sprintf('COM_JSPORTS_TEAM_SAVE_FAILED', $model->getError()), 'warning');
//             $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&layout=edit&id=' . $data['id'], false));
            
//             return false;
//         }
        
//         // Redirect the user and adjust session state based on the chosen task.
//         switch ($this->getTask()) {
//             //             case 'apply':
//             //                 // Check out the profile.
//             //                 $app->setUserState('com_users.edit.profile.id', $return);
            
//             //                 // Redirect back to the edit screen.
//             //                 $this->setMessage(Text::_('COM_USERS_PROFILE_SAVE_SUCCESS'));
            
//             //                 $redirect = $app->getUserState('com_users.edit.profile.redirect', '');
            
//             //                 // Don't redirect to an external URL.
//             //                 if (!Uri::isInternal($redirect)) {
//             //                     $redirect = null;
//             //                 }
            
//             //                 if (!$redirect) {
//             //                     $redirect = 'index.php?option=com_users&view=profile&layout=edit&hidemainmenu=1';
//             //                 }
            
//             //                 $this->setRedirect(Route::_($redirect, false));
//             //                 break;
            
//             default:
//                 // Clear the profile id from the session.
//                 $app->setUserState('com_jsports.edit.team.data', null);
                
//                 $redirect = $app->getUserState('com_jsports.edit.team.redirect', '');
                
//                 // Don't redirect to an external URL.
//                 if (!Uri::isInternal($redirect)) {
//                     $redirect = null;
//                 }
                
//                 if (!$redirect) {
//                     $redirect = 'index.php?option=com_jsports&view=teamprofile&id=' .  $data['id'];
//                 }
                
//                 // Redirect to the list screen.
//                 $this->setMessage(Text::_('COM_JSPORTS_TEAM_SAVE_SUCCESS'));
//                 $this->setRedirect(Route::_($redirect, false));
//                 break;
//         }
        
//         // Flush the data from the session.
//         $app->setUserState('com_jsports.edit.team.data', null);
    
        
        
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
        $teamid = $requestData['id'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.logoupload.data', null);
        
        // Redirect to user profile.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
    }
    
}
