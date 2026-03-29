<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Ads
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
use FP4P\Component\JSports\Site\Campaigns\CampaignDecorator;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Services\CampaignService;
use FP4P\Component\JSports\Site\Services\SponsorService;

class AdsManager {

    public static function renderCampaign($position) {
        // Get Campaigns from Database
        $campaigns = CampaignService::getEligibleCampaigns($position);
        
        if (empty($campaigns)) {
            return;
        }
        // If there are multiple campaigns for a given module, randomize the array and select one.
        // Only one ad/campaign can be displayed in one position.s
        if (count($campaigns) > 1) {
            $campaign = $campaigns[array_rand($campaigns,1)];
        } else {
            $campaign = $campaigns[0];
        }
        
        $cobj = new Campaign();
        $cobj->loadObject($campaign);

        $renderer = RendererFactory::create($cobj);
        
        echo $renderer->render($cobj, $position);
        
    }
    
    
    public static function renderSponsor($position) {
        
        $sponsorObj = SponsorService::getRandomSponsor();
        
        $sponsor = new Sponsor();
        $sponsor->loadObject($sponsorObj);
        
        $renderer = RendererFactory::create($sponsor);
        
        echo $renderer->render($sponsor, $position);
        
      
        return;
        
        //print_r($sponsor);
        exit;
        
        
        $logo = SponsorHelper::getLogoURL($sponsor->id, $sponsor->logo);
        $website = $sponsor->website;
        $sponsorship = Sponsorservice::getActiveSponsorship($sponsor->id);
        $sponsorshipid = $sponsor->sponsorshipid;
        
        //         $urlstring = "index.php?option=com_jsports&task=sponsor.click&id={$sponsor->id}";
        //         $clickurl = Route::_($urlstring);
        
        $clickurl = SponsorHelper::getClickUrl($sponsor->id);
        
    }
}

