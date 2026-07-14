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

interface ObserverIF
{
        
        //public function notify();
        public function notify($event,$args);
        
}

