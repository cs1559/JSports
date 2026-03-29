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
use FP4P\Component\JSports\Administrator\Table\SponsorshipPlanTable;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use Joomla\Filesystem\File;

class SponsorService
{

    /**
     * This function will return an individual row based on the SPONSOR ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\SponsorsTable|NULL
     */
    public static function getItem(int $id = 0): ?SponsorsTable
    {

        // $db = Factory::getDbo();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new SponsorsTable($db);

        $row = $table->load($id);

        if ($row) {
            return $table;
        }

        return null;
    }

    
    /**
     * This function is intended to return the active sponsorship for a sponsor.  This function intentionally excludes 
     * bolt-on plans.
     * 
     * @param number $id
     * @return object
     */
    public static function getActiveSponsorship($id) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('a.*, s.id as sponsorshipid, s.plancode, s.plantype, s.startdate, s.enddate');
        $query->from($db->quoteName('#__jsports_sponsors') . ' AS a, ' 
               . $db->quoteName('#__jsports_sponsorships') . ' AS s, ' 
                . $db->quoteName('#__jsports_sponsorship_plans') . ' AS p');
        
        $conditions = [
            $db->quoteName('a.id') . ' = :sponsorid',
            $db->quoteName('s.published') . ' = 1',
            $db->quoteName('p.bolton') . ' = 0',
            $db->quoteName('s.plancode') . ' = ' . $db->quoteName('p.plancode'),
            $db->quoteName('a.id') . ' = ' . $db->quoteName('s.sponsorid'),
            'CURDATE() BETWEEN ' . $db->quoteName('s.startdate') . ' AND ' . $db->quoteName('s.enddate')
        ];
        
        $query->where($conditions);
        $query->bind(':sponsorid', $id, ParameterType::INTEGER);
        $query->order($db->quoteName('a.name'));
        
        $db->setQuery($query);
        
        return $db->loadObject();
    }
    
    /**
     * This function will randomly return an active sponsor.  This does support balanced randomization to ensure
     * sponsor logos are presented as equally as possible on the site.
     * 
     * @return object
     */
    public static function getRandomSponsor() : object 
    {
        
        $params = ComponentHelper::getParams('com_jsports');
        $balancedenabled = $params->get('balancedrandomization', false);
        $leastusedfactor = $params->get('leastusedfactor',3);
        
        if ($balancedenabled) {
            $sponsors = self::getRandomActiveSponsors($leastusedfactor);
        } else {
            $sponsors = SponsorService::getActiveSponsors();
        }
        
        if (empty($sponsors)) {
            return [];
        }
        if (count($sponsors) > 1) {
            $sponsor = $sponsors[array_rand($sponsors,1)];
        } else {
            $sponsor = $sponsor[0];
        }
        
        return $sponsor;
        
    }

    /**
     * This function will return a list of sponsor records that are currently active based on the start/end date of their
     * sponsorships.  In addition, this will return the sponsors current sponsorship information.
     *
     * @return array<int, \stdClass>
     */
    private static function getRandomActiveSponsors($limit): ?array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $inner = $db->getQuery(true);
        $inner->select([
            'a.*',
            $db->quoteName('s.id', 'sponsorshipid'),
            $db->quoteName('s.impressions'),
            $db->quoteName('s.plancode'),
            $db->quoteName('s.plantype'),
            $db->quoteName('s.programid'),
            $db->quoteName('s.startdate'),
            $db->quoteName('s.enddate')
        ])
        ->from($db->quoteName('#__jsports_sponsors', 'a'))
        ->join(
            'INNER',
            $db->quoteName('#__jsports_sponsorships', 's') .
            ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('s.sponsorid')
            )
            ->where($db->quoteName('s.published') . ' = 1')
            ->where('CURDATE() BETWEEN ' . $db->quoteName('s.startdate') . ' AND ' . $db->quoteName('s.enddate'))
            ->order($db->quoteName('s.impressions') . ' ASC')
            ->setLimit((int) $limit);
        
        $query = $db->getQuery(true);
            
        $query->select('*')
            ->from('(' . $inner . ') AS t')
            ->order('RAND()');
        
        $db->setQuery($query);
        
        return $db->loadObjectList();
    }
    
    
    /**
     * This function will return a list of sponsor records that are currently active based on the start/end date of their
     * sponsorships.
     * 
     * @return array<int, \stdClass>
     */
    public static function getActiveSponsors(): ?array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('a.*, s.id as sponsorshipid');
        $query->from($db->quoteName('#__jsports_sponsors') . ' AS a, ' . $db->quoteName('#__jsports_sponsorships') . ' AS s');
        
        $conditions = [
            $db->quoteName('a.id') . ' = ' . $db->quoteName('s.sponsorid'),
            $db->quoteName('s.published') . ' = 1',
            'CURDATE() BETWEEN ' . $db->quoteName('s.startdate') . ' AND ' . $db->quoteName('s.enddate')
        ];
        
        $query->where($conditions);
        $query->order($db->quoteName('a.name'));
        
        $db->setQuery($query);
        
        return $db->loadObjectList();
    }
    
    
    /**
     *
     * @return array<int, \stdClass>
     */
    public static function getSponsorships(int $sponsorid): ?array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query->select('a.*, p.name as programname');
        $query->from($db->quoteName('#__jsports_sponsorships') . ' AS a,' . $db->quoteName('#__jsports_programs') . ' AS p ');

        $conditions = array(
            $db->quoteName('a.sponsorid') . ' = ' . $db->quote($sponsorid),
            $db->quoteName('a.programid') . ' = ' . $db->quoteName('p.id')
        );
        $query->where($conditions);
        $query->order("p.id desc, a.plancode asc");

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * This function will return an array of asset information for a given sponsor.
     *
     * @return array<int, \stdClass>
     */
    public static function getAssets(int $sponsorid, $imagesonly = false): ?array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query->select('sa.*, a.name as sponsorname');
        $query->from($db->quoteName('#__jsports_sponsors') . ' AS a,' . $db->quoteName('#__jsports_sponsor_assets') . ' AS sa ');

        $conditions = array(
            $db->quoteName('a.id') . ' = ' . $db->quote($sponsorid),
            $db->quoteName('a.id') . ' = ' . $db->quoteName('sa.sponsorid')
        );
        if ($imagesonly) {
            $conditions[] = $db->quoteName('sa.mimetype') . ' like \'image%\'';
        }
        $query->where($conditions);
        $query->order("sa.title asc");

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    // public static function deleteSponsorship(int $id = 0) : bool {

    // if ($id === 0) {
    // LogService::error('Sponsorship Record ID ' . $id . ' is required - delete attempted');
    // return false;
    // }

    // // @TODO - add rule for when a DELETE operation CANNOT be executed.
    // $app = Application::getInstance();

    // $db = Factory::getContainer()->get(DatabaseInterface::class);
    // $table = new SponsorshipsTable($db);
    // if (!$table->load($id)) {
    // LogService::error("Sponsorship ID {$id} not found - delete attempted");
    // return false;
    // }

    // try {
    // $rc = $table->delete($id);

    // if ($rc) {
    // LogService::info("Sponsorship " . $id . " - sponsorship [ " . $table->name . "] deleted ");
    // return true;
    // } else {
    // LogService::error("Error deleting sponsorship " . $id . " - sponsorship [ " . $table->name . "] ");
    // return false;
    // }
    // } catch (\Throwable $e) {
    // LogService::error("Exception deleting sponsorship (" . $id . ") - " . $e->getMessage());
    // return false;
    // }
    // // return $rc;
    // }

    /**
     * This function will update the Sponsor's filename.
     *
     * @param int $id
     * @param string $name
     * @return boolean
     */
    public static function updateLogoFilename(int $id, string $name): bool
    {
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

            $query->bind(':sponsorid', $id, ParameterType::INTEGER)->bind(':filename_value', $name, ParameterType::STRING);

            $db->setQuery($query);
            $db->execute();
            return true;
        } catch (\Exception $e) {
            LogService::error($e->getMessage());
            return false;
        }
        // return true;
    }

    /**
     * This function will physically delete the sponsors logo from the filesystem and clear out the logo name on
     * the database record.
     * 
     * @param int $key
     * @return boolean
     */
    public static function deleteLogo(int $key) : bool 
    {
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
            return SponsorService::updateLogoFilename($key, '');
        }
    }

    /**
     * This function will delete a specific asset.
     * 
     * @param number $assetid
     * @return boolean
     */
    public static function deleteAsset($assetid = 0) : bool
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $asset = new SponsorassetsTable($db);

        if (! $asset->load($assetid)) {
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
    public static function deleteAssetFile(SponsorassetsTable $table) : bool
    {
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

    /**
     * This function defines specific rules on whether or not a customer can add a sponsorship.
     * 
     * @todo Need to look at this and check whether nor not logic needs to be added to look at start/end dates.
     * 
     * @param int $sponsorid
     * @param int $programid
     * @param string $plancode
     * @return bool
     */
    public static function canAddSponsorship(int $sponsorid, string $plancode, int $programid = null) : bool
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $table = new SponsorshipPlanTable($db);
        $table->load(['plancode' => $plancode]);
        if ($table->bolton) {
            return true;
        }
        if (self::hasPrimarySponsorship($sponsorid)) {
            return false;
        }
        return true;
        
    }

    
    /**
     * This function will test to determine if a sponsor has an active "sponsorship".  A sponsor can only have ONE (1) 
     * active plan (primary) during the calendar year.  Any number of "bolt on" plans can be added at anytime.
     *   
     * @param int $sponsorid
     * @param int $programid
     * @param int $excludedid
     * @return bool
     */
    public static function hasPrimarySponsorship(int $sponsorid,  int $programid = 0): bool
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        // $query = $db->getQuery(true)
        // ->select('COUNT(*)')

        $query->select('count(*)');
        $query->from($db->quoteName('#__jsports_sponsorships') . ' AS a,' . $db->quoteName('#__jsports_sponsorship_plans') . ' AS sp ')
            ->where($db->quoteName('a.plancode') . ' = ' . $db->quoteName('sp.plancode'))
            ->where($db->quoteName('sponsorid') . ' = :sponsorid')
//             ->where($db->quoteName('programid') . ' = :seasonid')
            ->where('CURDATE() BETWEEN ' . $db->quoteName('a.startdate') . ' AND ' . $db->quoteName('a.enddate'))
            ->where($db->quoteName('a.published') . ' = 1')
            -> // <- adjust field
        where($db->quoteName('bolton') . ' = 0');

        $query->bind(':sponsorid', $sponsorid, ParameterType::INTEGER);
        

//         $query->bind(':seasonid', $programid, ParameterType::INTEGER);

        $db->setQuery($query);

        return (int) $db->loadResult() > 0;
    }


    /**
     * This function will return an object that shows the current entitlements for a given sponsor.
     * @param int $sponsorid
     * @return bool
     */
    public static function getEntitlements(int $sponsorid, $debug = false): object
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        // $query = $db->getQuery(true)
        // ->select('COUNT(*)')
        
        $query->select('a.*,sp.entitlements');
        $query->from($db->quoteName('#__jsports_sponsorships') . ' AS a,' . $db->quoteName('#__jsports_sponsorship_plans') . ' AS sp ')
        ->where($db->quoteName('a.plancode') . ' = ' . $db->quoteName('sp.plancode'))
        ->where($db->quoteName('sponsorid') . ' = :sponsorid')
//         ->where($db->quoteName('programid') . ' = :seasonid')
        ->where($db->quoteName('a.published') . ' = 1')
        ->where('CURDATE() BETWEEN ' . $db->quoteName('a.startdate') . ' AND ' . $db->quoteName('a.enddate'))
            ;        
        
        $query->bind(':sponsorid', $sponsorid, ParameterType::INTEGER);
//         $query->bind(':seasonid', $programid, ParameterType::INTEGER);
        
        if ($debug) {
            echo (string) $query;
        }
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        
        $entitlements = new \stdClass();
        $entitlements->totalcampaigns = 0;
        $entitlements->allowbulletins = false;
        $entitlements->positions = [];
        
        foreach ($rows as $row) {
            $data = json_decode($row->entitlements, true);
            $entitlements->totalcampaigns = $entitlements->totalcampaigns + $data['max_campaigns'];
            if ($data['bulletins']) {
                $entitlements->allowbulletins = true;
            }
            $entitlements->positions = array_merge($entitlements->positions,$data['positions']);
        }

        $entitlements->positions = array_values(array_unique($entitlements->positions));
        return $entitlements;
    }

}

