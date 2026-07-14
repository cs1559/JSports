<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

namespace FP4P\Component\JSports\Site\Services;

/**
 * The LogService class is a "helper" class that encapsulates the logging functions. The
 * intention is to simplify both site/admin classes to write a log record if desired.
 * @author Chris Strieter
 *
 */

/**
 * REVISION HISTORY:
  */

use FP4P\Component\JSports\Administrator\Table\DivisionsTable;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;

class AuditService
{
      
    
    /**
     * This function will purge log records from the database and return the number of rows
     * affected.
     * 
     * @param number $logdays
     * @return array
     */
    public static function purge($logdays = 200) {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
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

