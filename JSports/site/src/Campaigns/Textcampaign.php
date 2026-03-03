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

class Textcampaign extends Campaign
{
 
    public function __construct($data) {
        $this->loadObject($data);    
    }
    
    public $content = "";
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    public function toHtml($position) {
        
        $output = '';
        
        if (strlen($this->customcss) > 0) {
            $output .= "<style>" . $this->customcss . "</style>";
        }
        $output .= "<div class='jsports-text-campaign-container' style='width:100%; '>";
        $output .= "<div id='jsports-campaign-{$this->id}-{$position}' class='jsports-text-campaign-content {$this->classname}'";
        $output .= $this->content; // <-- fixed
        $output .= "</div>";
        $output .= "</div>";
        
        return $output;
    }
    
}

