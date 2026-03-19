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
use Joomla\CMS\Router\Route;
use FP4P\Component\JSports\Site\Services\CampaignService;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;

class TextWithLinkCampaign extends Campaign
{

    public $url = "";
    public $assetid = 0;
    
    const TYPE = "T2";
    
    public function __construct($data) {
        $this->loadObject($data);
    }
    

    /**
     * @return string
     */
//     private function getAssetUrl()
//     {
        
//         $asset = CampaignService::getAsset($this->sponsorid, $this->assetid);
        
//         $url = SponsorHelper::getAssetURL($this->sponsorid, $asset->filename);
//         return $url;
//     }
    
    public function toHtml($position) {
                
        $output = '';
        
        if (strlen($this->customcss) > 0) {
            $output .= "<style>" . $this->customcss . "</style>";
        }

        $output .= "<div id='jsports-campaign-{$position}' class='jsports-campaign-slot jsports-campaign-img-content {$this->classname}'>";
        
        $clickurl = $this->getClickUrl();
        $output .= "<a class='jsports-img-link' target='_blank' href='{$clickurl}' rel='noopener noreferrer'>";
        
        $output .= $this->content;
        
        $output .= "</a>";
        $output .= "</div>";
        
        return $output;
    }
    
}

