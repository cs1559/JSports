<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

namespace FP4P\Component\JSports\Site\Campaigns;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;

final class CampaignHelper
{
    
    static function translateCampaignType($code)
    {
        static $map = [
            Textcampaign::TYPE => 'Text',
            TextWithLinkCampaign::TYPE => 'Text with Link',
            Imagecampaign::TYPE => 'Image',
            ImageWithTextCampaign::TYPE => 'Image with Text',
            'I' => 'Image',
            'D' => 'Text With Link',
            'P' => 'Promo Banner',
        ];
        
        return $map[$code] ?? '*error*';
    }
    

}

