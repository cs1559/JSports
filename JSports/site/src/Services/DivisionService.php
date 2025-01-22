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

/**
 * DivisionService - This is a service class that exposes certain functions that
 * various components within the applicaiton that can call statically.
 * 
 * REVISION HISTORY:
 * 2025-01-16  Cleaned up code and added inline comments.
 */

use FP4P\Component\JSports\Administrator\Table\DivisionsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

class DivisionService
{
    
    /**
     * This function will return an individual row based on the DivisionID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\DivisionsTable|NULL
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
        
    /**
     * This function will return a list of 'published' divisions within a specific program.
     *
     * @param unknown $programid
     * @return array
     */
    public static function getDivisionList($programid, $group = null) {
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_divisions') . ' AS p ');
        if (is_null($group)) {
            $conditions = array(
                $db->quoteName('p.published') . ' in (1) ',
                $db->quoteName('p.programid') . ' = ' . $programid,
            );
        } else {
            $conditions = array(
                $db->quoteName('p.published') . ' in (1) ',
               $db->quoteName('p.programid') . ' = ' . $programid,
                $db->quoteName('p.agegroup') . ' = ' . $group
            );
        }
        $query->where($conditions);
        $query->order("ordering asc");
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
}

