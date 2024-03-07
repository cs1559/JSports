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

use FP4P\Component\JSports\Site\Objects\ObserverIF;

class BaseObserver implements ObserverIF
{
    public function notify($event, $args) {
        if (method_exists(get_class($this),$event)) {
            return call_user_func(get_class($this). "::" . $event, $args);
        }
        return;
    }
}

