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

use FP4P\Component\JSports\Site\Objects\Events\EventDispatcher;
use FP4P\Component\JSports\Site\Services\MailService;
use FP4P\Component\JSports\Site\Objects\Logger\DatabaseLogger;

class Application
{
    
    private static $instance = null;
    private $dispatcher;
    
    private function __construct() {
        // nothing extra to do.
        
        $this->dispatcher = EventDispatcher::getInstance();
    }
    
    public static function getLogger(){
        return DatabaseLogger::getInstance();
    }
    
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Application();
        }
        
        return self::$instance;
    }
    
    
    /**
     * Ths function will use the dispatcher to trigger the event notifications.
     * 
     * @param string $eventName
     * @param array $args
     * @return boolean
     */
    public function triggerEvent($eventName,$args = [] ) {
        /*
         * onAfterPostScore
         * onAfterRegistration
         * onAfterOwnerUpdate
         * onAfterGameDelete
         * 
         * onAfterPostScore
         */

        $this->dispatcher->trigger($eventName,$args);
        
        
        return true;
    }
    
    
}

