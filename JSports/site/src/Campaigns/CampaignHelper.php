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

final class CampaignHelper
{
    
    static function translateCampaignType($code)
    {
        static $map = [
            'T' => 'Text',
            'I' => 'Image',
            'D' => 'Document',
        ];
        
        return $map[$code] ?? '*error*';
    }
    

}

