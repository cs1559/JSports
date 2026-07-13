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

namespace FP4P\Component\JSports\Site\Helpers;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Router;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Router\Route;

final class RegistrationHelper
{
    /**
     * This function translates an optional input field to return a constant value of 'N/A'.
     *
     * @param string $code
     * @return string
     */
    public static function translateOptionalValue($value = '') : string
    {
 
        // PHP >= 8.0
        if ($value === null || $value === '') {
            return 'N/A';
        }
        
        if (!strlen($value)) {
            return 'N/A';
        }
        
        return $value;
    }
    
    public static function translateSkillLevel($value) : string {
        $skill = "";
        switch ($value) {
            case 'R':
                $skill = "Red";
                break;
            case 'W':
                $skill = "White";
                break;
            case 'B':
                $skill = "Blue";
                break;
            case 'E':
                $skill = "Elite";
                break;
            default:
                $skill = "N/A";
                break;
        }
        
        return $skill;
    }

    
}

