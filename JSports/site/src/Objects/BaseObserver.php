<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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

