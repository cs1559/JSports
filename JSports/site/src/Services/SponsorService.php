<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @todo        Needs additional refactoring
 *
 */

namespace FP4P\Component\JSports\Site\Services;

use FP4P\Component\JSports\Administrator\Table\SponsorassetsTable;
use FP4P\Component\JSports\Administrator\Table\SponsorsTable;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Application;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Administrator\Table\SponsorshipsTable;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use Joomla\Filesystem\File;

class SponsorService
{
    
    /**
     * This function will return an individual row based on the PROGRAM ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\ProgramsTable|NULL
     */
    public static function getItem(int $id = 0) : ?SponsorsTable {
        
//         $db = Factory::getDbo(); 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new SponsorsTable($db);
                
        $row = $table->load($id);
        
        if ($row) {
            return $table;
        }
               
        return null;
    }
    
//     /**
//      * This function will return an array of objects that represent a list of programs that have not
//      * been completed.
//      *
//      * @return array<int, stdClass>
//      */
//     public static function getPrograms($inclregistrations = false) {
//         $db = Factory::getContainer()->get(DatabaseInterface::class);
//         //$db = Factory::getDbo();
//         $query = $db->getQuery(true);
        
//         $query->select('p.*');
//         $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
//         $conditions = array(
//             $db->quoteName('p.registrationonly') . ' = 0',
//             $db->quoteName('p.published') . ' in (1) '
//         );
// //         if ($activeonly) {
// //             $conditions[] = $db->quoteName('p.status') . ' = "A"';
// //         }
//         $query->where($conditions);
//         $query->order('id desc');
//         $db->setQuery($query);
//         return $db->loadObjectList();
        
//     }
    
//     /**
//      * This function will return an array of objects that represent a list of programs that have not
//      * been completed.
//      *
//      * @return array<int, stdClass>
//      */
//     public static function getNonCompletedPrograms(bool $activeonly = false) {
//         $db = Factory::getContainer()->get(DatabaseInterface::class);
//         //$db = Factory::getDbo();
//         $query = $db->getQuery(true);
        
//         $query->select('p.*');
//         $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
//         $conditions = array(
//             $db->quoteName('p.status') . ' <> "C"',
//             $db->quoteName('p.published') . ' in (1) '
//         );
//         if ($activeonly) {
//             $conditions[] = $db->quoteName('p.status') . ' = "A"';
//         }
//         $query->where($conditions);
//         $query->order('id asc');
//         $db->setQuery($query);
//         return $db->loadObjectList();
        
//     }
    
    /**
     * @return array<int, \stdClass>
     */
    public static function getSponsorships(int $sponsorid) : ?array {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
    
        $query->select('a.*, p.name as programname');
        $query->from($db->quoteName('#__jsports_sponsorships') . ' AS a,' .
            $db->quoteName('#__jsports_programs') . ' AS p '
            );
        
        $conditions = array(
            $db->quoteName('a.sponsorid') . ' = ' . $db->quote($sponsorid),
            $db->quoteName('a.programid') . ' = ' . $db->quoteName('p.id'), 
        );
        $query->where($conditions);
        $query->order("p.id desc, a.planlevel asc");
    
        $db->setQuery($query);
        return $db->loadObjectList();
    
    }
  
    /**
     * @return array<int, \stdClass>
     */
    public static function getAssets(int $sponsorid) : ?array {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('sa.*, a.name as sponsorname');
        $query->from($db->quoteName('#__jsports_sponsors') . ' AS a,' .
            $db->quoteName('#__jsports_sponsor_assets') . ' AS sa '
            );
        
        $conditions = array(
            $db->quoteName('a.id') . ' = ' . $db->quote($sponsorid),
            $db->quoteName('a.id') . ' = ' . $db->quoteName('sa.sponsorid'),
        );
        $query->where($conditions);
        $query->order("sa.title asc");
        
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    
    
//     public static function deleteSponsorship(int $id = 0) : bool {
        
//         if ($id === 0) {
//             LogService::error('Sponsorship Record ID ' . $id . ' is required - delete attempted');
//             return false;
//         }
        
//         // @TODO - add rule for when a DELETE operation CANNOT be executed.
//         $app = Application::getInstance();
        
//         $db = Factory::getContainer()->get(DatabaseInterface::class);
//         $table = new SponsorshipsTable($db);
//         if (!$table->load($id)) {
//             LogService::error("Sponsorship ID {$id} not found - delete attempted");
//             return false;
//         }
        
//         try {
//             $rc = $table->delete($id);
            
//             if ($rc) {
//                 LogService::info("Sponsorship " . $id . " - sponsorship [ " . $table->name . "] deleted ");
//                 return true;
//             } else {
//                 LogService::error("Error deleting sponsorship " . $id . " - sponsorship [ " . $table->name . "] ");
//                 return false;
//             }
//         } catch (\Throwable $e) {
//             LogService::error("Exception deleting sponsorship (" . $id . ") - " . $e->getMessage());
//             return false;
//         }
//         //         return $rc;
//     }    

/**
    * This function will update the bulletin's attachment filename.
     *
     * @param int $id
     * @param string $name
     * @return boolean
     */
    public static function updateLogoFilename(int $id, string $name) : bool {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $fields = array(
            $db->quoteName('logo') . '= :filename_value'
        );
                
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = :sponsorid'
        );
                
        try {
            $query->update($db->quoteName('#__jsports_sponsors'))
    ->set($fields)
    ->where($conditions);
    
    $query->bind(':sponsorid', $id, ParameterType::INTEGER)
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


public static function deleteLogo(int $key) {
    
    $filepath = SponsorHelper::getLogoFolder($key);
       
    // Delete the entire logo folder
    if (Folder::exists($filepath)) {
        if (Folder::delete($filepath)) {
            if (SponsorService::updateLogoFilename($key, '')) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return SponsorService::updateLogoFilename($key,'');
    }
    
}


public static function deleteAsset($assetid = 0) {
    
    $db = Factory::getContainer()->get(DatabaseInterface::class);
    $asset = new SponsorassetsTable($db);
    
    if (!$asset->load($assetid)) {
        throw new \Exception("Asset record ({$assetid}) cound not be located");
    }
    
    if (file_exists(SponsorHelper::getAssetFolder($table->sponsorid) . $asset->filename)) {
        if (self::deleteAssetFile($asset)) {
            // Underlying file has been deleted.
            if ($asset->delete($assetid)) {
                return true;
            } else {
                throw new \Exception("Attempt to delete asset file ({$asset->filename}) was not deleted");
            }
        } else {
            throw new \Exception("Asset file ({$asset->filename}) cound not be found");
        }
    } else {
        if ($asset->delete($assetid)) {
            return true;
        } else {
            throw new \Exception("Attempt to delete asset file ({$asset->filename}) was not deleted");
        }
    }
    
    return true;
    
}

/**
 * This function will delete the underlying file related to a specific asset.
 * 
 * @param SponsorassetsTable $table
 * @return boolean
 */
public static function deleteAssetFile(SponsorassetsTable $table) {
    
    $folder = SponsorHelper::getAssetFolder($table->sponsorid);
    $file = $folder . $table->filename; 
    
    if (file_exists($file)) {
        if (File::delete($file)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

    
   
     
}

