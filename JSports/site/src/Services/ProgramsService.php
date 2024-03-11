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

use FP4P\Component\JSports\Administrator\Table\ProgramsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

class ProgramsService
{
    
    /**
     * This function will return an individual row based on the PROGRAM ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Administrator\Table\ProgramsTable|NULL
     */
    public static function getItem($id = 0) {
        
        $db = Factory::getDbo();
        $programs = new ProgramsTable($db);
                
        $row = $programs->load($id);
        
        if ($row) {
            return $programs;
        }
               
        return null;
    }
    
    /**
     * This function will return an array of objects that represent a list of programs that have not
     * been completed.
     *
     * @return unknown
     */
    public static function getNonCompletedPrograms($activeonly = false) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.status') . ' <> "C"',
            $db->quoteName('p.published') . ' in (1) '
        );
        if ($activeonly) {
            $conditions[] = $db->quoteName('p.status') . ' = "A"';
        }
        $query->where($conditions);
        $query->order('id asc');
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    /**
     * This function will return a default program.  The default program will be the most recent
     * non-completed program.
     *
     * @return unknown
     */
    public static function getDefaultProgram() {
        
        $programs = ProgramsService::getNonCompletedPrograms();
        
        return $programs[0];
    }
    
    
    public static function getProgramList() {
        
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.published') . ' in (0,1) '
        );
        $query->where($conditions);
        $query->order("name desc");
        $db->setQuery($query);
        return $db->loadAssocList();
        
        
    }
    
    
    public static function getMostRecentProgram() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_programs') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.published') . ' in (1) '
        );
        $query->where($conditions);
        $query->order("id desc");
        $db->setQuery($query);
        $rows = $db->loadAssocList();
        return $rows[0];
        
    }
    
    
    
}

