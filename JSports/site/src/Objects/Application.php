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

use FP4P\Component\JSports\Site\Objects\EventDispatcher;

class Application
{
    
    private static $instance = null;
    
    private function __construct() {
        
    }
    /**
     * This function will return the Event Dispatcher.
     * 
     * @return unknown
     */
//     public static function getDispatcher() {
        
//         $dispatcher = EventDispatcher::getInstance();
            
//         return $dispatcher;
        
//     }
    
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Application();
        }
        
        return self::$instance;
    }
    
    
    public function triggerEvent($eventName,$args = [] ) {
        /*
         * onAfterPostScore
         * onAfterRegistration
         * onAfterOwnerUpdate
         * onAfterGameDelete
         * 
         */
        
        return true;
    }
    
}

