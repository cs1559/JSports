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
namespace FP4P\Component\JSports\Site\Objects\Logger;

abstract class Logger 
{
    
    var $level = 1;
    var $enabled = 1;
    
    /**
     * @return boolean $enabled
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param number $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int $level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param number $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    abstract function info($msg);
    abstract function warning($msg);
    abstract function error($msg);
    abstract function critical($msg);
    abstract function write($msg);
    abstract function debug($msg);
    abstract function writeObject($obj);
    
}
