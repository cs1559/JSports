<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Services;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Table\SponsorshipsTable;

class SponsorshipService
{
    
    /**
     * This function will return an individual row based on the SPONSORSHIP ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\SponsorshipsTable|NULL
     */
    public static function getItem(int $id = 0) : ?SponsorshipsTable {
        
//         $db = Factory::getDbo(); 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new SponsorshipsTable($db);
                
        $row = $table->load($id);
        
        if ($row) {
            return $table;
        }
               
        return null;
    }
    
    
    public static function incrementImpressions($sid) : void {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
        ->update($db->quoteName('#__jsports_sponsorships'))
        ->set($db->quoteName('impressions') . ' = ' . $db->quoteName('impressions') . ' + 1')
        ->where($db->quoteName('id') . ' = :sid')
        ->bind(':sid', $sid, ParameterType::INTEGER);
        
        $db->setQuery($query)->execute();
    }
    
  
    public static function click($sid) : void {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
        ->update($db->quoteName('#__jsports_sponsorships'))
        ->set($db->quoteName('clicks') . ' = ' . $db->quoteName('clicks') . ' + 1')
        ->where($db->quoteName('id') . ' = :sid')
        ->bind(':sid', $sid, ParameterType::INTEGER);
        
        $db->setQuery($query)->execute();
    }
    
     
}

