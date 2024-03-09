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

namespace FP4P\Component\JSports\Site\Services;

use FP4P\Component\JSports\Administrator\Table\RostersTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

class RosterService
{
    
    public static function getRostersTable() {
        $db = Factory::getDbo();
        return new RostersTable($db);
       
    }
    
    
    /**
     * This function will return an individual row based on the ROSTER ITEM ID
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\RostersTable
     */
    public function getItem($id = 0) {
        
        $db = Factory::getDbo();
        $table = new RostersTable($db);
        
        $row = $table->load($id);
        
        if ($row) {
            return $table;
        }
               
        return null;
    }
    
    /**
     * This function will DELETE a specific row within the ROSTERS table.
     *
     * @param number $id  Item ID
     */
    public static function delete($id = 0) {
        
        $db = Factory::getDbo();
        
        $query = $db->getQuery(true);
        
        $conditions = array(
            $db->quoteName('id') . '=' .$db->quote($id));
        
        $query->delete($db->quoteName('#__jsports_rosters'));
        $query->where($conditions);
        
        $db->setQuery($query);
        
        return $db->execute();
    }
      
    
    public static function getRosterStaff($teamid, $programid) {
        return RosterService::getRosterDataByType($teamid, $programid, 'S');
    }

    public static function getRosterPlayers($teamid, $programid) {
        return RosterService::getRosterDataByType($teamid, $programid, 'P');
    }
    
    
    private static function getRosterDataByType($teamid, $programid, $classification) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_rosters') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.classification') . ' = ' . $db->quote($classification),
            $db->quoteName('p.teamid') . ' = ' . $db->quote($teamid),
            $db->quoteName('p.programid') . ' = ' . $db->quote($programid)
        );
        $query->where($conditions);
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    
}
