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

use FP4P\Component\JSports\Administrator\Table\RegistrationsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class RegistrationService
{
   
    public static function getRegistrationTable() {
        $db = Factory::getDbo();
        return new RegistrationsTable($db);
      
    }
    
    /**
     * This function will return an individual row based on the Registration ID.
     *
     * @param number $id
     * @return \FP4P\Component\JSports\Site\Services\Registration\RegistrationsTable|NULL
     */
    public function getItem($id = 0) {
        
        $db = Factory::getDbo();
        $registrations = new RegistrationsTable($db);
        $row = $registrations->load($id);
        
        if ($row) {
            return $registrations;
        }
               
        return null;
    }
    
    
    /**
     * This function returns a boolean whether or not anyone can register on the platform.  there
     * are two (2) primary tests to determine if registration is avaiable:
     *
     * 1.  Registration is completely turned off at the league/organization level.
     * 2.  Checks to see if there are ANY programs currently accepting registration(s).
     *
     * @return boolean
     */
    public function isRegistrationAvailable() {
    
        $params = ComponentHelper::getParams('com_jsports');
        $regenabled = $params->get('registrationenabled');

        // See if Registration is enabled at the component level.
        if (!$regenabled) {
            Factory::getApplication()->enqueueMessage("Platform does not have registration enabled", 'error');
            return false;
        }
        
        // Create a new query object.
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        
        /*
        $id = 1;
        
        // Select the required fields from the table.
        $query->select($db->quoteName('registrationenabled'));
        $query->from($db->quoteName('#__jsports_leagues'));
        $query->where($db->quoteName('id') . ' = :id' );
        $query->bind(':id', $id, ParameterType::INTEGER);
        
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $row = $db->loadAssoc();

        
        if (!$row['registrationenabled']) {
            Factory::getApplication()->enqueueMessage("Platform does not have registration enabled", 'error');
            return false;
        }
        */
        
        $query = $db->getQuery(true);
        // Query Programs table to see if there are any programs current open for registration
        $query->select($db->quoteName(array('name','registrationopen')));
        $query->from($db->quoteName('#__jsports_programs') . ' AS a');
        $query->where($db->quoteName('registrationopen') . ' = 1' );
        $db->setQuery($query);
        
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $row = $db->loadObjectList();
        
        
        if (!count($row)) {
            Factory::getApplication()->enqueueMessage("No programs are currently accepting registrations", 'error');
            return false;
        }
        
        return true;
    }
    
    
    public static function getRegistrationsByProgram($programid)
    {
        
        $program = ProgramsService::getItem($programid);
        
        $table = '#__jsports_registrations';

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.*');
        $query->from($db->quoteName($table) . ' AS a ' );
        $conditions = array(
            $db->quoteName('a.programid') . ' = ' . $db->quote($programid)
        );

        $query->where($conditions);
        
        $query->order('a.grouping, a.teamname');

        // $query->where($conditions);
        $db->setQuery($query);
        
        return $db->loadAssocList();
    }
    
    
    
}

