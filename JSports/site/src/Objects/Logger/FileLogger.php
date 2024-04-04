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

class FileLogger extends Logger 
{
    
    private $filename = null;
    
    private function __construct() {  }
    
    static function getInstance($filename) {
        static $instance;
        if (!is_object( $instance )) {
            $instance = new self();
            $instance->setFilename($filename);
        }
        return $instance;
    }
    
    private function setFilename($fn) {
        $this->filename = $fn;
    }
    private function getFilename() {
        return $this->filename;
    }
    public function warning($msg)
    {
        $this->write("[WARNING] " . $msg);
    }

    public function critical($msg)
    {
        $this->write($msg);
    }

    public function error($msg)
    {
        $this->write("[ERROR] " . $msg);
    }

    public function write($msg)
    {
        if (!$this->isEnabled()) {
            return;
        }
        $time = @date('[d/M/Y:H:i:s]');
        
        // open file
        $fd = fopen($this->getFilename(), 'a');
        
        // write string
        fwrite($fd, $time . " " . $msg . PHP_EOL);
        
        // close file
        fclose($fd);
    }
    
    public function debug($msg) {
        if ($this->getLevel() > 2) {
            $this->write("[DEBUG] " . $msg);
        }
        return;
    }

    public function writeObject($obj) {
        if (!$this->isEnabled()) {
            return;
        }
        
        ob_start ();
        print_r($obj);
        $content = ob_get_contents ();
        ob_end_clean ();
               
        $time = @date('[d/M/Y:H:i:s]');
        
        // open file
        $fd = fopen($this->getFilename(), 'a');
        
        fwrite($fd, $time . " [OBJECT]  " . get_class($obj) . " " . PHP_EOL);
        fwrite($fd, "Object: " . $content);
        fclose($fd);
    }
    
    public function info($msg)
    {
        $this->write("[INFO] " . $msg);
    }

}
