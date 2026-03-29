<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Ads;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Services\CampaignService;
use Joomla\CMS\Router\Route;


class Campaign extends Ad
{

    public $title = '';
    public $sponsorid;
    public $sponsorshipid = 0; 
    public $campaigntype = 0;
    public $layout = 'default';
    public $assetid = 0;
    public $positions = '';
    public $impressions = 0;
    public $clicks = 0;
    public $startdate;
    public $enddate;
    public $published = 0;
    public $customcss;
    public $classname;
    public $data;
    public $content;
    public $url;
    public $sponsorlogo;
    public $sponsorname;
    public $sponsorurl;
    public $link;
    
    public function __construct() {
        $this->type="Campaign";
    }
    
//     public function getClickUrl() {
        
//         $params = ComponentHelper::getParams('com_jsports');
//         $secret = $params->get('secretkey', "jsports");
               
//         $id = $this->id;
//         $ts = time();
        
//         $token = hash_hmac('sha256', $id . '|' . $ts, $secret);
//         $urlstring = "index.php?option=com_jsports&task=campaign.click&id={$this->id}&ts={$ts}&sig={$token}";
//         $clickurl = Route::_($urlstring);   
//         return $clickurl;
//     }
    
    /**
     * @return string
     */
    public function getAssetUrl()
    {
        
        if (!$this->assetid) {
            return "";
        }
        $asset = CampaignService::getAsset($this->sponsorid, $this->assetid);
        
        $url = SponsorHelper::getAssetURL($this->sponsorid, $asset->filename);
        return $url;
    }
    
    /**
     * @return mixed
     */
    public function getCustomcss()
    {
        return $this->customcss;
    }

    /**
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return number
     */
    public function getSponsorshipid()
    {
        return $this->sponsorshipid;
    }

    /**
     * @return number
     */
    public function getCampaigntype()
    {
        return $this->campaigntype;
    }

    /**
     * @return number
     */
    public function getAssetid()
    {
        return $this->assetid;
    }

    /**
     * @return string
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @return number
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return number
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @return mixed
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * @return mixed
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * @return number
     */
    public function getPublished()
    {
        return $this->published;
    }

    public function getSponsorUrl() {
        return $this->sponsorurl;    
    }
    
    public function getLayout() {
        static $map = [
            'T1' => 'textonly',
            'T2' => 'textwithlink',
            'I1' => 'imageonly',
            'I2' => 'imagewithtext',
            'P' => 'promobanner',
        ];
        
        return $this->layout;
        
    }
    
    
    public function getRedirectUrl() {
        
        $redirecturl = [
            'S' => $this->sponsorurl,
            'A' => $this->getAssetUrl(),
            'X' => $this->url,
        ];
        
        return $redirecturl[$this->link] ?? $this->sponsorurl;

    }

    public function getSponsorLogoUrl() {
        return SponsorHelper::getLogoURL($this->sponsorid, $this->sponsorlogo);
    }

}

