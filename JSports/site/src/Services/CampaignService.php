<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace FP4P\Component\JSports\Site\Services;

use FP4P\Component\JSports\Administrator\Table\SponsorassetsTable;
use FP4P\Component\JSports\Administrator\Table\SponsorsTable;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Table\SponsorshipsTable;
use FP4P\Component\JSports\Administrator\Table\CampaignsTable;
use Joomla\CMS\Uri\Uri;

class CampaignService
{
    
    /**
     * This function will return an individual row based on the PROGRAM ID.
     *
     * @param int $id
     * @return \FP4P\Component\JSports\Administrator\Table\CampaignsTable|NULL
     */
    public static function getItem(int $id = 0) : ?CampaignsTable {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new CampaignsTable($db);
                
        $row = $table->load($id);
        
        if ($row) {
            return $table;
        }
               
        return null;
    }

    /**
     * This function will return a single published campaign record, joined with its
     * sponsor's name, logo, and website, based on the campaign ID.
     *
     * @param int $pk Campaign ID
     * @return \stdClass|null
     */
    public static function getCampaign(int $pk) {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('c.*,s.name as sponsorname, s.logo as sponsorlogo, s.website as sponsorurl');
        $query->from($db->quoteName('#__jsports_campaigns') . ' AS c, ' .
            $db->quoteName('#__jsports_sponsors') . ' AS s');
        
        $conditions = array(
            $db->quoteName('c.sponsorid') . ' = ' . $db->quoteName('s.id'),
            $db->quoteName('c.published') . ' in (1) ',
            $db->quoteName('c.id') . ' = :pk'
        );
        
        $query->where($conditions);
        $query->bind(':pk', $pk, ParameterType::INTEGER);
        
        $db->setQuery($query);
        return $db->loadObject();
    }   
    
    /**
     * This function will return an array of objects that represent all campaigns for a given
     * position.  The campaign MUST be a published campaign.
     *
     * @param string $position The campaign position slot to match against (e.g. 'sidebar', 'header')
     * @param int|null $filter Optional sponsor ID to restrict results to a single sponsor
     * @return array<int, stdClass>
     */
    public static function getEligibleCampaigns(string $position, ?int $filter = null) {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('c.*,s.name as sponsorname, s.logo as sponsorlogo, s.website as sponsorurl');
        $query->from($db->quoteName('#__jsports_campaigns') . ' AS c, ' .
            $db->quoteName('#__jsports_sponsors') . ' AS s');
        
        $likePosition = '%' . $position . '%';
        
        $conditions = array(
            $db->quoteName('c.sponsorid') . ' = ' . $db->quoteName('s.id'),
            $db->quoteName('c.positions') . ' LIKE :position',
            $db->quoteName('c.published') . ' in (1) ',
            $db->quoteName('c.enddate') . ' >= CURDATE()'
        );
        
        if (!is_null($filter)) {
            $conditions[] = $db->quoteName('c.sponsorid') . ' = :filter';
        }
        
        $query->where($conditions);
        $query->bind(':position', $likePosition, ParameterType::STRING);
        
        if (!is_null($filter)) {
            $query->bind(':filter', $filter, ParameterType::INTEGER);
        }
        
        $db->setQuery($query);
        return $db->loadObjectList();
    }

//     public static function getEligibleCampaigns($position, $filter = null) {

//         $db = Factory::getContainer()->get(DatabaseInterface::class);
//         $query = $db->getQuery(true);

//         /*
//          *         $query->from($db->quoteName('#__jsports_sponsors') . ' AS a,' .
//             $db->quoteName('#__jsports_sponsor_assets') . ' AS sa '
//             );
//          */
//         $query->select('c.*,s.name as sponsorname, s.logo as sponsorlogo, s.website as sponsorurl');
//         $query->from($db->quoteName('#__jsports_campaigns') . ' AS c, ' .
//             $db->quoteName('#__jsports_sponsors') . 'AS s');
//         $conditions = array(
//             $db->quoteName('c.sponsorid') . ' = ' . $db->quoteName('s.id'),
//             $db->quoteName('c.positions') . ' like \'%' . $position . '%\'',
//             $db->quoteName('c.published') . ' in (1) ',
//             $db->quoteName('c.enddate') . ' >= CURDATE()'
//         );
        
//         if (!is_null($filter)) {
//             $conditions[] = $db->quoteName('c.sponsorid') . ' = ' . $filter;
//         }
// //         if ($activeonly) {
// //             $conditions[] = $db->quoteName('p.status') . ' = "A"';
// //         }
//         $query->where($conditions);
// //         $query->order('id desc');
//         $db->setQuery($query);
//         return $db->loadObjectList();
        
//     }
    
    
    /**
     * This function will return a single sponsor asset record, joined with its sponsor's name,
     * based on the sponsor ID and asset ID.
     *
     * @param int $sponsorid
     * @param int $assetid
     * @return \stdClass|null
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
    
    /**
     * This function will return the URL string that can be used to retrieve a sponsor's
     * asset file (e.g. logo, banner image) via a browser.
     *
     * @param int $sponsorid
     * @param string $filename
     * @return string
     */
    public static function getAssetURL(int $sponsorid, string $filename) {
        
        $imageFolder = "/media/com_jsports/images/sponsors/assets/";
                
        return Uri::root() . $imageFolder . 'sponsor-' . $sponsorid .'/' . $filename;
        
    }
    
    
    /**
     * This function will increment the impressions counter for a given campaign by 1.
     *
     * @param int $campaignid
     * @return void
     */
    public static function incrementImpressions(int $campaignid) : void {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
        ->update($db->quoteName('#__jsports_campaigns'))
        ->set($db->quoteName('impressions') . ' = ' . $db->quoteName('impressions') . ' + 1')
        ->where($db->quoteName('id') . ' = :campaignid')
        ->bind(':campaignid', $campaignid, ParameterType::INTEGER);
        
        $db->setQuery($query)->execute();
    }
    
  
    /**
     * This function will increment the clicks counter for a given campaign by 1.
     *
     * @param int $campaignid
     * @return void
     */
    public static function click(int $campaignid) : void {
        
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
        ->update($db->quoteName('#__jsports_campaigns'))
        ->set($db->quoteName('clicks') . ' = ' . $db->quoteName('clicks') . ' + 1')
        ->where($db->quoteName('id') . ' = :campaignid')
        ->bind(':campaignid', $campaignid, ParameterType::INTEGER);
        
        $db->setQuery($query)->execute();
    }
    
     
}