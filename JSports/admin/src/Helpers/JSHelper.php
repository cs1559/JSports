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

    	if (str_contains($value,'PM')) {
    		return $value;
    	}
    	if (str_contains($value,'AM')) {
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
}

