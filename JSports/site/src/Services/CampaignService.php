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
use FP4P\Component\JSports\Administrator\Table\CampaignsTable;
use Joomla\CMS\Uri\Uri;

class CampaignService
{
    
    /**
     * This function will return an individual row based on the PROGRAM ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\CampaignsTable|NULL
     */
    public static function getItem(int $id = 0) : ?CampaignsTable {
        
//         $db = Factory::getDbo(); 
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new CampaignsTable($db);
                
        $row = $table->load($id);
        
        if ($row) {
            return $table;
        }
               
        return null;
    }
    
    /**
     * This function will return an array of objects that represent all campaigns for a given
     * position.  The campaign MUST be a published campaign.
     *
     * @return array<int, stdClass>
     */
    public static function getEligibleCampaigns($position) {

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('c.*');
        $query->from($db->quoteName('#__jsports_campaigns') . ' AS c ');
        $conditions = array(
            $db->quoteName('c.positions') . ' like \'%' . $position . '%\'',
            $db->quoteName('c.published') . ' in (1) '
        );
//         if ($activeonly) {
//             $conditions[] = $db->quoteName('p.status') . ' = "A"';
//         }
        $query->where($conditions);
//         $query->order('id desc');
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    
    /**
     * @return array<int, \stdClass>
     */
    public static function getAsset(int $sponsorid, int $assetid)
    {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('sa.*, a.name as sponsorname');
        $query->from($db->quoteName('#__jsports_sponsors') . ' AS a,' .
            $db->quoteName('#__jsports_sponsor_assets') . ' AS sa '
            );
        
        $conditions = array(
            $db->quoteName('a.id') . ' = ' . $db->quote($sponsorid),
            $db->quoteName('a.id') . ' = ' . $db->quoteName('sa.sponsorid'),
            $db->quoteName('sa.id') . ' = :assetid',
        );
        $query->where($conditions);
        $query->order("sa.title asc");
        $query->bind(':assetid', $assetid, ParameterType::INTEGER);
        
        $db->setQuery($query);
        return $db->loadObject();
        
    }
    
    public static function getAssetURL($sponsorid, $filename) {
        
        $imageFolder = "/media/com_jsports/images/sponsors/assets/";
                
        return Uri::root() . $imageFolder . 'sponsor-' . $sponsorid .'/' . $filename;
        
    }
    
    
    public static function incrementImpressions($campaignid) : void {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
        ->update($db->quoteName('#__jsports_campaigns'))
        ->set($db->quoteName('impressions') . ' = ' . $db->quoteName('impressions') . ' + 1')
        ->where($db->quoteName('id') . ' = :campaignid')
        ->bind(':campaignid', $campaignid, ParameterType::INTEGER);
        
        $db->setQuery($query)->execute();
    }
    
  
    public static function click($campaignid) : void {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
        ->update($db->quoteName('#__jsports_campaigns'))
        ->set($db->quoteName('clicks') . ' = ' . $db->quoteName('clicks') . ' + 1')
        ->where($db->quoteName('id') . ' = :campaignid')
        ->bind(':campaignid', $campaignid, ParameterType::INTEGER);
        
        $db->setQuery($query)->execute();
    }
    
     
}

