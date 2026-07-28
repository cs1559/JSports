<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Objects;

use FP4P\Component\JSports\Site\Events\EventDispatcher;
use FP4P\Component\JSports\Site\Logger\DatabaseLogger;
use Joomla\CMS\Component\ComponentHelper;

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
     * This is a helper function that can be used to check if the component is in TEST mode.
     * If it is, certain functionality may be limited.  An example is when sending emails.  Emails
     * would only be sent to certain email addresses versus the the actual recipients.
     * @return bool
     */
    public static function inTestMode() : bool {
        $params = ComponentHelper::getParams('com_jsports');
        $testmode = $params->get('testmode', "jsports");
        
        return $testmode;
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

