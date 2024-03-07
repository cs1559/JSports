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

use FP4P\Component\JSports\Administrator\Table\DivisionsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

class DivisionService
{
    
    /**
     * This function will return an individual row based on the PROGRAM ID.
     * 
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\ProgramsTable|NULL
     */
    public static function getItem($id = 0) {
        
        $db = Factory::getDbo();
        $divisions = new DivisionsTable($db);
                
        $row = $divisions->load($id);
        
        if ($row) {
            return $divisions;
        }
               
        return null;
    }
        
    public static function getDivisionList($programid) {
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_divisions') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.published') . ' in (1) ',
            $db->quoteName('p.programid') . ' = ' . $programid,
        );
        $query->where($conditions);
        $query->order("ordering asc");
        $db->setQuery($query);
        $rows = $db->loadAssocList();
        return $rows;
        
    }
    
    
    
    
    
}

