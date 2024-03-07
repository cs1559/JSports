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

class Observable
{
    
    var $observers = null;
    
    protected function __construct() {
        $this->observers = array();
    }
    
    
    function trigger($event, & $args = null)
    {
        // Iterate through the _observers array
        foreach ($this->observers as $observer) {
            $observer->notify($event, $args);
        }
    }
    
    /**
     * Attach an observer object
     *
     * @access public
     * @param object $observer An observer object to attach
     * @return void
     * @since 1.5
     */
    function attach( ObserverIF &$observer)
    {
        // Make sure we haven't already attached this object as an observer
        if (is_object($observer))
        {
            $class = get_class($observer);
            foreach ($this->observers as $check) {
                if (is_a($check, $class)) {
                    return;
                }
            }
            $this->observers[] =& $observer;
        } else {
            $this->observers[] =& $observer;
        }
    }
    
    /**
     * Detach an observer object
     *
     * @access public
     * @param object $observer An observer object to detach
     * @return boolean True if the observer object was detached
     * @since 1.5
     */
    function detach( ObserverIF $observer)
    {
        // Initialize variables
        $retval = false;
        
        $key = array_search($observer, $this->observers);
        
        if ( $key !== false )
        {
            unset($this->observers[$key]);
            $retval = true;
        }
        return $retval;
    }
    
}

