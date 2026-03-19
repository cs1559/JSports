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

class ImageWithTextCampaign extends Campaign
{

    const TYPE = "I2";
   
    public function __construct($data) {
        $this->loadObject($data);
    }
    


    
    public function toHtml($position) {
        
        $url = $this->getAssetUrl();
        
        $output = '';
        
        if (strlen($this->customcss) > 0) {
            $output .= "<style>" . $this->customcss . "</style>";
        }

        $output .= "<div id='jsports-campaign-{$position}' class='jsports-campaign-slot jsports-campaign-img-content {$this->classname}'>";
        
        $clickurl = $this->getClickUrl();
        $output .= "<a class='jsports-img-link' target='_blank' href='{$clickurl}' rel='noopener noreferrer'>";
        $output .= "</div>";

        $output .= $this->content;
        $output .= "</a>";
        return $output;
    }
    
}

