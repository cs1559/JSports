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
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Router\Route;

final class SponsorHelper
{
    /**
     * This function translates the game status value into something more meaningful.
     *
     * @param string $code
     * @return string
     */
    public static function translatePlancode($code = '') : string
    {
        
        static $map = [
            'C' => 'Comp',
            'G' => 'Gold',
            'S' => 'Silver',
            'B' => 'Bronze',
            'P' => 'Platinum',
            'X' => 'Bolt-on',
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
        $pdfimage = "/media/com_jsports/images/pdf-icon.png";
        
        $fullfilename = JPATH_ROOT . 'sponsor-' . $sponsorid .'/' . $filename;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if ($ext === 'pdf') {
            return Uri::root() . '/' . $pdfimage;
        }
        
        return Uri::root() . $imageFolder . 'sponsor-' . $sponsorid .'/' . $filename;
        
    }
    
    public static function getClickUrl($sponsorid) {
        
        $params = ComponentHelper::getParams('com_jsports');
        $secret = $params->get('secretkey', "jsports");
        
        $id = $sponsorid;
        $ts = time();
        
        $token = hash_hmac('sha256', $id . '|' . $ts, $secret);
        $urlstring = "index.php?option=com_jsports&task=sponsor.click&id={$id}&ts={$ts}&sig={$token}";
        $clickurl = Route::_($urlstring);
        return $clickurl;
    }
    
}

