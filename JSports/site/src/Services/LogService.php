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

namespace FP4P\Component\JSports\Site\Services;

use FP4P\Component\JSports\Administrator\Table\DivisionsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;

/**
 * The LogService class is a "helper" class that encapsulates the logging functions. The 
 * intention is to simplify both site/admin classes to write a log record if desired.
 * @author cs155
 *
 */
class LogService
{
    
    public static function error($msg) {
        $logger = Myapp::getLogger();
        $logger->error($msg);
    }
   
    public static function info($msg) {
        $logger = Myapp::getLogger();
        $logger->info($msg);
    }

    public static function warning($msg) {
        $logger = Myapp::getLogger();
        $logger->warning($msg);
    }
    
    public static function debug($msg) {
        $logger = Myapp::getLogger();
        $logger->debug($msg);
    }
    
    public static function critical($msg) {
        $logger = Myapp::getLogger();
        $logger->critical($msg);
    }
   
    public static function writeArray(array $data, $context = '') {
        
        $msg = " [" . $context . "] " . json_encode($data);
//         if (json_validate($data)) {
//             $msg = " DATA: " . $data;
//         } elseif (is_array($data)) {
//                 $msg = " DATA: " . json_encode($data);
//             } else {
//                 return;
//         }
   
        
        $logger = MyApp::getLogger();
        $logger->data($msg);
    }
    
    
    /**
     * This function will purge log records from the database and return the number of rows
     * affected.
     * 
     * @param number $logdays
     * @return unknown
     */
    public static function purge($logdays = 200) {
        
        $db    = Factory::getDbo();
        
        $query = $db->getQuery(true);
        
        $sql = "delete from " . $db->quoteName("#__jsports_action_logs")
        . " where logdate < (curdate() - interval " . $logdays . " day)";
        
        $query->setQuery($sql);
        $db->setQuery($query);
        $result = $db->execute();
        
        $rows = $db->getAffectedRows();
        
        return $rows;
    }
}

