<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Objects;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

class RouteHelper
{
    
    public static function _getRoute($url) {
        
        $params = ComponentHelper::getParams('com_jsports');
        $itemid = $params->get('itemid');
        
        $newurl = $url . '&Itemid=' . $itemid;
    
        return Route::_($newurl);
        
    }
    
}

