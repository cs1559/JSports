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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;

final class SponsorHelper
{
    /**
     * This function translates the game status value into something more meaningful.
     *
     * @param string $code
     * @return string
     */
    public static function translatePlanLevel($code = '') : string
    {
        
        static $map = [
            'D' => 'Default',
            'G' => 'Gold',
            'S' => 'Silver',
            'B' => 'Bronze',
        ];
        
        return $map[$code] ?? '*error*';
    }

    public static function translatePlanType($code = '') : string
    {
        
        static $map = [
            'L' => 'League',
            'D' => 'Division',
            'T' => 'Team',
        ];
        
        return $map[$code] ?? '*error*';
    }

    public static function getLogoFolder($sponsorid, $alias = null) {
        
        $imageFolder = "/media/com_jsports/images/sponsors/logos/";
     
//         if (!empty($alias)) {
            $sponsorfolder = 'sponsor-' . $sponsorid . '/';
//         } else {
//             $sponsorfolder = trim($alias) . '-' . $sponsorid;
//         }
            
        $filepath = Folder::makeSafe( $imageFolder . $sponsorfolder );
        
        $filepath = JPATH_ROOT . $filepath;
        
        return $filepath;

    }
    
    /**
     *
     * @param number $key
     * @param string $filename
     * @return string
     */
    public static function getLogoURL($sponsorid, $filename) {
        
        $imageFolder = "/media/com_jsports/images/sponsors/logos/";
        
        return Uri::root() . $imageFolder . 'sponsor-' . $sponsorid .'/' . $filename;
        
    }

    public static function getAssetFolder($sponsorid, $alias = null) {
        
        $imageFolder = "/media/com_jsports/images/sponsors/assets/";
        
        //         if (!empty($alias)) {
        $sponsorfolder = 'sponsor-' . $sponsorid . '/';
        //         } else {
        //             $sponsorfolder = trim($alias) . '-' . $sponsorid;
        //         }
        
        $filepath = Folder::makeSafe( $imageFolder . $sponsorfolder );
        
        $filepath = JPATH_ROOT . $filepath;
        
        return $filepath;
        
    }
    
    /**
     *
     * @param number $key
     * @param string $filename
     * @return string
     */
    public static function getAssetURL($sponsorid, $filename) {
        
        $imageFolder = "/media/com_jsports/images/sponsors/assets/";
        
        return Uri::root() . $imageFolder . 'sponsor-' . $sponsorid .'/' . $filename;
        
    }
    
    
}

