<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Services;

/**
 * DivisionService - This is a service class that exposes certain functions that
 * various components within the applicaiton that can call statically.
 */

use FP4P\Component\JSports\Administrator\Table\DivisionsTable;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class DivisionService
{
    
    /**
     * This function will return an individual row based on the DivisionID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\DivisionsTable|NULL
     */
    public static function getItem(int $id = 0) : ?DivisionsTable {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
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
     * @param number $programid
     * @param number $group     - Age group
     * @param number $exclude   - Division ID to exclude
     * @return array<int, array<string, mixed>> | null
     */
    public static function getDivisionList($programid, $group = null, $exclude = null) : array {
        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_divisions') . ' AS p ');
        if (is_null($group)) {
            $conditions = array(
                $db->quoteName('p.published') . ' in (1) ',
                $db->quoteName('p.programid') . ' = :programid' 
            );
        } else {
            $conditions = array(
                $db->quoteName('p.published') . ' in (1) ',
               $db->quoteName('p.programid') . ' = :programid ', 
                $db->quoteName('p.agegroup') . ' = :group'
            );
        }
        if (!is_null($exclude)) {
            $conditions[] = $db->quoteName('p.id') . ' <> :excludeid' ;
        }
        $query->where($conditions);
        $query->order("ordering asc");
        if (!is_null($group)) {
            $query->bind(':group',$group, ParameterType::INTEGER);
        }
        $query->bind(':programid',$programid, ParameterType::INTEGER);
        if (!is_null($exclude)) {
            $query->bind(':excludeid',$exclude, ParameterType::INTEGER);
        }
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
}

