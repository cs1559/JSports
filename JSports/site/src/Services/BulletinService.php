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
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
// use Joomla\CMS\Filesystem\Path;
use FP4P\Component\JSports\Site\Services\LogService;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;

class BulletinService
{
   
    public static function getBulletinsTable() : BulletinsTable
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        return new BulletinsTable($db);
    }
    
    /**
     * This function will return an individual row based on the Bulletin ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\BulletinsTable|NULL
     */
    public static function getItem(int $id = 0) : ?BulletinsTable {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $bulletins = new BulletinsTable($db);
                
        $row = $bulletins->load($id);
        
        if ($row) {
            return $bulletins;
        }               
        return null;
    }

    /**
     * This function will delete an individual bulletin record in the database.
     * NOTE:  deleting any underlying attachment is handled elsewhere.
     * 
     * @param int $id
     * @return bool
     */
    public static function delete(int $id = 0) : bool {
        
        $logger = Myapp::getLogger();
        if ($id === 0) {
            $logger->error('Bulletin Record ID ' . $id . ' is required ');
            return false;
        }
        
        $svc = new BulletinService();
        $item = $svc->getItem($id);
        if ($item === null) {
            $logger->error("Bulletin record not found (id=$id)");
            return false;
        }
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $conditions = array(
            $db->quoteName('id') . ' = :id');
        
        $query->delete($db->quoteName('#__jsports_bulletins'));
        $query->where($conditions);
        $query->bind(':id', $id, ParameterType::INTEGER);
        $db->setQuery($query);
        
        try {
            $db->execute();
            $logger->info('Deleting bulletin item - ' . $item->title . ' ID: ' . $id);
            return true;
        } catch (\Throwable $e) {
            $logger->error('Bulletin delete failed - Record ID ' . $id . ' - ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * This function returns the path (incl. folder) defined at the component level where the bulletin attachments are stored.
     * The full path is defined as the value from the component options PLUS "Bulletin-" appanded with the bulletin ID.  
     * However, the arguemnt (key) can be really any value passed by the calling client.
     * 
     * @param string $key
     * @return string
     */
    public static function getBulletinFilePath(int $key) : string {
        $params = ComponentHelper::getParams('com_jsports');
        $attachmentdir = trim((string) $params->get('attachmentdir',''));
        
        $path = JPATH_ROOT . '/' . $attachmentdir;

        $value = rtrim($path); // optional: remove trailing whitespace
        if (str_ends_with($path, '/') || str_ends_with($path, '\\')) {
            $endswithsep = true;
        } else {
            $endswithsep = false;
        }
        
//         if (!$endswithsep) {
//             $filepath = Folder::makeSafe( $path . '/Bulletin-' . $key .'/');
//         } else {
// 	       $filepath = Folder::makeSafe( $path . '/Bulletin-' . $key .'/');
//         }
        $filepath = $path . '/Bulletin-' . $key .'/';
        return $filepath;
    }
    
    /**
     * This function will return the URL string that can be used to retrieve the bulletins
     * attachment via a browser.
     * 
     * @param string $key Same as the bulletinid
     * @param string $filename
     * @return string
     */
    public static function getBulletinAttachmentURL(int $key, string $filename) : string {
        $params = ComponentHelper::getParams('com_jsports');
        $attachmentdir = trim((string) $params->get('attachmentdir',''));
        
        return Uri::root() . $attachmentdir . '/Bulletin-' . $key . '/' . rawurlencode($filename);
                
    }
    
    
    /**
     * This function will remove the attachments underlying folder and all files within it.
     * @param string $key
     * @return boolean
     */
    public static function deleteAttachmentFolder(int $key) {
        
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

    /**
     * This function will blank out the attachment filenmae for a specific bulletin.
     * @param int $id
     * @return bool
     */
    public static function clearAttachmentFilename(int $id) : bool {
        if ($id === 0) {
            return false;
        }
        return BulletinService::updateAttachmentFilename($id, "");    
    }
    
    /**
     * This function will update the bulletin's attachment filename.
     * 
     * @param int $id
     * @param string $name
     * @return boolean
     */
    public static function updateAttachmentFilename(int $id, string $name) : bool {

        $db = Factory::getContainer()->get(DatabaseInterface::class);
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
            
            $query->bind(':bulletinid', $id, ParameterType::INTEGER)
                ->bind(':filename_value', $name, ParameterType::STRING);
            
            $db->setQuery($query);
            $db->execute();
            return true;
            
        } catch (\Exception $e) {
            LogService::error($e->getMessage());
            return false;
        }
//         return true;
    }



}




