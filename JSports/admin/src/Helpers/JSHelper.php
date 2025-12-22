<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Helpers;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;

class JSHelper
{
    /**
     * This function translates the game status value into something more meaningful.
     * 
     * @param string $code
     * @return string
     */
    public static function translateGameStatus($code = "") 
    {
        
        switch ($code) {
            case 'S':
                return 'Scheduled';
                break;
            case 'C':
                return 'Complete';
                break;
            case 'X':
                return 'Cancelled';
                break;
            case 'R':
                return 'Rain Out';
                break;
            default:
                return '*error*';
                break;
        }       
    }

    /**
     * This function translates the roster classication into a readable value.
     *
     * @param string $value (1 or 0)
     * @return string
     */
    public static function translateYesNo($value = 0)
    {
        
        switch ($value) {
            case 1:
                return 'Yes';
                break;
            default:
                return 'No';
                break;
        }
    }

    /**
     * This function translates the roster classication into a readable value.
     *
     * @param string $code
     * @return string
     */
    public static function translateRosterClassification($type = "")
    {
        
        switch ($type) {
            case 'S':
                return 'Staff';
                break;
            case 'P':
                return 'Player';
                break;
            default:
                return '*error*';
                break;
        }
        
    }
    
    
    /**
     * This function is used to present the 24 hour time into the normal HH:MM PM/AM format. 
     * 
     * @param unknown $value
     * @return unknown|string
     */
    public static function displayGameTime($value) {

    	if (str_contains(strtoupper($value),'PM')) {
    		return $value;
    	}
    	if (str_contains(strtoupper($value),'AM')) {
    		return $value;
    	}
    
    	$retval = '';
    	$time = explode(':',$value);
    
    	if ($time[0] > 12) {
    		$retval = $time[0] - 12 . ':' . $time[1] . ' PM';
    	} else {
    	    if ($time[0] == 12) {
    	        $retval = $time[0] . ':' . $time[1] . ' PM';
    	    } else {
    		  $retval = $time[0] . ':' . $time[1] . ' AM';
    	    }
    	}
        return $retval;
    }
    
    
    /**
     * This function will return the version number of the component.
     * @return string
     */
    public static function getVersion() {
        $xml_path = JPATH_ADMINISTRATOR . '/components/com_jsports/jsports.xml';
        $xml_obj = new \SimpleXMLElement(file_get_contents($xml_path));
        return strval($xml_obj->version); 
    }
    
    /**
     * This function will return the version number of the component.
     * @return string
     */
    public static function getReleasedate() {
        $xml_path = JPATH_ADMINISTRATOR . '/components/com_jsports/jsports.xml';
        $xml_obj = new \SimpleXMLElement(file_get_contents($xml_path));
        return strval($xml_obj->creationDate);
    }

    /**
     * This function translates the game status value into something more meaningful.
     *
     * @param string $code
     * @return string
     */
    public static function translateBulletinType($code = "")
    {
        
        switch ($code) {
            case 'G':
                return 'General';
                break;
            case 'T':
                return 'Tournament';
                break;
            case 'Y':
                return 'Tryouts';
                break;
            default:
                return '*error*';
                break;
        }
    }
    
    /**
     * @deprecated
     * @param unknown $key
     * @return string
     */
    public static function getBulletinFilePath($key) {
        $params = ComponentHelper::getParams('com_jsports');
        $attachmentdir = $params->get('attachmentdir');
        
        $value = rtrim($attachmentdir); // optional: remove trailing whitespace
        
        if (substr($attachmentdir, -1) !== '/') {
            $attachmentdir .= '/';
        }
        
if (substr($attachmentdir, 0, 1) !== '/') {
    $attachmentdir = '/' . $attachmentdir;
}

        $filepath = Folder::makeSafe( $attachmentdir . '/Bulletin-' . $key .'/');
        
        $filepath = JPATH_ROOT . $filepath;
        
        return $filepath;
    }

    /**
     * @deprecated
     * @param unknown $key
     * @param unknown $filename
     * @return string
     */
    public static function getBulletinAttachmentURL($key, $filename) {
        $params = ComponentHelper::getParams('com_jsports');
        $attachmentdir = $params->get('attachmentdir');
        
        $filepath =  Uri::root() . $attachmentdir . '/Bulletin-' . $key .'/' . $filename;
        
        return $filepath;
    }
}

