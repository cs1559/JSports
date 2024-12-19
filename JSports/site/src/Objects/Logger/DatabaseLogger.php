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

use FP4P\Component\JSports\Site\Objects\Logger\Logger;
use Joomla\CMS\Factory;

class DatabaseLogger extends Logger 
{
    
    private function __construct() {  }
    
    static function getInstance() {
        static $instance;
        if (!is_object( $instance )) {
            $instance = new self();
         }
        return $instance;
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
        
        $db = Factory::getDbo();
        $user = Factory::getUser();
        
        $query = $db->getQuery(true);
                
        if (!$this->isEnabled()) {
            return;
        }

        $datetime = date_create()->format('Y-m-d H:i:s');
        
        $query = $db->getQuery(true);
        $columns = array(
            'id',
            'logdate',
            'userid',
            'username',
            'msg',
        );

        /**
         * if for whatever reason the user is unknown, go ahead and populate a user name.  The assumption is that the component
         * still allows a "save" if a timeout occurs while the user is on an edit screen.  
         * 
         * NOTE:  the save functions need to be "tightened" up to check for an authenticate session before allowing a save to 
         * be performed.
         */
        if (!$user->id) {
            $user->name = "Unknown";
        }
        
        $values = array(
            0,
            $db->quote($datetime), // teamname
            $db->quote($user->id), // alias
            $db->quote($user->username), // city
            $db->quote($msg), // state
        );
        
        $query->insert($db->quoteName('#__jsports_action_logs'))
        ->columns($db->quoteName($columns))
        ->values(implode(',', $values));
        
        $db->setQuery($query);
        $db->execute();
        
        
//         // open file
//         $fd = fopen($this->getFilename(), 'a');
        
//         // write string
//         fwrite($fd, $time . " " . $msg . PHP_EOL);
        
//         // close file
//         fclose($fd);
    }
    
    public function debug($msg) {
        if ($this->getLevel() > 2) {
            $this->write("[DEBUG] " . $msg);
        }
        return;
    }

    
    public function writeObject($obj) {
        
//         if (!$this->isEnabled()) {
//             return;
//         }
        
//         ob_start ();
//         print_r($obj);
//         $content = ob_get_contents ();
//         ob_end_clean ();
               
//         $time = @date('[d/M/Y:H:i:s]');
        
//         // open file
//         $fd = fopen($this->getFilename(), 'a');
        
//         fwrite($fd, $time . " [OBJECT]  " . get_class($obj) . " " . PHP_EOL);
//         fwrite($fd, "Object: " . $content);
//         fclose($fd);
    }
    
    public function info($msg)
    {
        $this->write("[INFO] " . $msg);
    }

}
