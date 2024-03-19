<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Objects\Events;

use FP4P\Component\JSports\Site\Objects\Observable;
use FP4P\Component\JSports\Site\Objects\Events\GameObserver;

class EventDispatcher extends Observable
{
    protected function __construct() {
        parent::__construct();
    }
    
    public static function getInstance() {
        static $instance;
        if (!is_object( $instance )) {
            $instance = new EventDispatcher();
            $obs = new GameObserver();
            $instance->attach($obs);
        }
        return $instance;
    }
    
}

