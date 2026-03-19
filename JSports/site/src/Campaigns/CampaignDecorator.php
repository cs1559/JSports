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
use Joomla\CMS\Factory;
use Joomla\CMS\WebAsset\WebAssetManager;


class CampaignDecorator
{
    /** var array $campaigns */
    public $campaigns = [];
    public $campaign;
    public $totalcampaigns = 0;
    public $position='default';
    
    public function __construct($position, $campaigns) {
               
        if (is_array($campaigns)) {
            $this->campaigns = $campaigns;
            $this->totalcampaigns = count($campaigns);
        } else {
            $this->campaign = $campaigns;
        }
        
        $this->position = $position;
    }
    
    
    public function decorate() {
        
        // Get the WebAssetManager instance
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_jsports');
        $wa->useStyle('com_jsports.campaigns.style');

        $campaign = CampaignFactory::createCampaign($this->campaign);

        $output = "";
        
        if (!$campaign instanceOf PromoBarCampaign) {
            $output .= "<div class='jsports-campaign-container jsports-pos-" 
                . htmlspecialchars($this->position, ENT_QUOTES, 'UTF-8') ."'>";
        }
        
        $output .= $campaign->toHtml($this->position);
    
        if (!$campaign instanceOf PromoBarCampaign) {
            $output .= "</div>";
        }
        return $output;
	}

}

