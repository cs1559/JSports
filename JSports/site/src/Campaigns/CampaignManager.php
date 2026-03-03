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

namespace FP4P\Component\JSports\Site\Campaigns;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use FP4P\Component\JSports\Site\Services\CampaignService;

class CampaignManager
{
    /**
     * This function is responsible for rendering a campaign for a given position.
     * @param string $position
     * @return void|string
     */
    public static function renderCampaigns($position = '') 
    {
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
        $decorator = new CampaignDecorator($position, $campaign);
        $output = $decorator->decorate();

        CampaignService::incrementImpressions($campaign->id);
        
        return $output;
        
        // Build each campaign using a factory
        // return value
        
    }

}

