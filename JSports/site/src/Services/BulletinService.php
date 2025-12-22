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

namespace FP4P\Component\JSports\Site\Services;

/**
 * DivisionService - This is a service class that exposes certain functions that
 * various components within the applicaiton that can call statically.
 * 
 * REVISION HISTORY:
 * 2025-01-16  Cleaned up code and added inline comments.
 */

use FP4P\Component\JSports\Administrator\Table\BulletinsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use FP4P\Component\JSports\Site\Services\LogService;

class BulletinService
{
    
    /**
     * This function will return an individual row based on the Bulletin ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\BulletinsTable|NULL
     */
    public static function getItem($id = 0) {
        
        $db = Factory::getDbo();
        $bulletins = new BulletinsTable($db);
                
        $row = $bulletins->load($id);
        
        if ($row) {
            return $bulletins;
        }
               
        return null;
    }

    public static function getBulletinFilePath($key) {
        $params = ComponentHelper::getParams('com_jsports');
        $attachmentdir = $params->get('attachmentdir');
        
        $value = rtrim($attachmentdir); // optional: remove trailing whitespace
        
        if (substr($attachmentdir, -1) !== '/') {
            $attachmentdir .= '/';
        }
        
        if (substr($attachmentdir, 0, 1) !== '/') {
            $attachmentdir = '/' . $attachmentdir;
        }
        
        $filepath = Folder::makeSafe( $attachmentdir . '/Bulletin-' . $key .'/');
        
        $filepath = JPATH_ROOT . $filepath;
        
        return $filepath;
    }
    
    public static function getBulletinAttachmentURL($key, $filename) {
        $params = ComponentHelper::getParams('com_jsports');
        $attachmentdir = $params->get('attachmentdir');
        
        $filepath =  Uri::root() . $attachmentdir . '/Bulletin-' . $key .'/' . $filename;
        
        return $filepath;
    }
    
    
    /**
     * This function will remove the attachments underlying folder and all files within it.
     * @param unknown $key
     * @return boolean
     */
    public static function deleteAttachmentFolder($key) {
        
        $filepath = BulletinService::getBulletinFilePath($key);
        
        if (Folder::exists($filepath)) {
            if (Folder::delete($filepath)) {
                if (BulletinService::clearAttachmentFilename($key)) {
                    LogService::info("Attachment folder deleted and database record updated for bulletin id " . $key);
                    return true;
                } else {
                    LogService::warning("Attachment folder deleted - database record update failed for bulletin id " . $key);
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return BulletinService::clearAttachmentFilename($key);
        }
        
    }

    
    public static function clearAttachmentFilename($id) {
        return BulletinService::updateAttachmentFilename($id, "");    
    }
    
    /**
     * This function will update the bulletin's attachment filename.
     * 
     * @param unknown $id
     * @param unknown $name
     * @return boolean
     */
    public static function updateAttachmentFilename($id, $name) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $fields = array(
            $db->quoteName('attachment') . '= :filename_value'
        );
        
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = :bulletinid'
        );
        
        try {
            $query->update($db->quoteName('#__jsports_bulletins'))
            ->set($fields)
            ->where($conditions);
            
            $query->bind(':bulletinid', $id, ParameterType::INTEGER)->bind(':filename_value', $name, ParameterType::STRING);
            
            $db->setQuery($query);
            
            $db->execute();
            return true;
        } catch (Exception $e) {
            LogService::error($e->getMessage());
            return false;
        }
        return true;
    }
}

