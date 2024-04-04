<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Services\RegistrationService;
use Joomla\CMS\Filter\OutputFilter; 

/**
 * Methods supporting a list of mywalks records.
 *
 * @since  1.6
 */
class RegistrationsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     \Joomla\CMS\MVC\Controller\BaseController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name', 'a.name',
				'teamname', 'a.teamname',
			    'programid', 'a.programid',
			    'published', 'a.published',
			    'agegroup', 'a.agegroup',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.id', $direction = 'asc')
	{
	    
	    $app = Factory::getApplication();
	    
	    $programid = $app->input->get('programid', 0, 'int');
	    if (empty($programid)) {
	       $programid = $app->getUserState('com_jsports.programid');
	    }
	    $this->setState('programid', $programid);
	    // keep the walk_id for adding new visits
	    $app->setUserState('com_jsports.programid', $programid);
	    
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$agegroup = $this->getUserStateFromRequest($this->context . '.filter.agegroup', 'filter_agegroup', '');
		$this->setState('filter.agegroup', $agegroup);
		
		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.programid');
		$id .= ':' . $this->getState('filter.agegroup');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.6
	 */
	
	protected function getListQuery()
	{
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    // Select the required fields from the table.
	    $query->select(
	        $this->getState(
	            'list.select',
	            'a.*'
	            )
	        );
	    $query->from($db->quoteName('#__jsports_registrations') . ' AS a');
	        
	    // Filter by published state
	    $published = (string) $this->getState('filter.published');
	    
	    if (is_numeric($published))
	    {
	        $query->where($db->quoteName('a.published') . ' = :published');
	        $query->bind(':published', $published, ParameterType::INTEGER);
	    }
	    elseif ($published === '')
	    {
	        $query->whereIn($db->quoteName('a.published'), array(0, 1));
	    }
	    
	    // Filter by PROGRAM.
	    $programid = $this->getState('filter.programid');
	    if (!empty($programid)) {	        
	        $query->where($db->quoteName('a.programid') . ' = :programid');
	        $query->bind(':programid', $programid, ParameterType::INTEGER);
	    }

	    // Filter by AGE GROUP
	    $agegroup = (string) $this->getState('filter.agegroup');
	    if (is_numeric($agegroup))
	    {
	        $query->where($db->quoteName('a.agegroup') . ' = :agegroup');
	        $query->bind(':agegroup', $agegroup, ParameterType::INTEGER);
	    }
	    
	    // Filter by search in TEAMNAME.
	    $search = $this->getState('filter.search');
	    
	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where($db->quoteName('a.teamname') . ' LIKE :search')
	        ->bind(':search', $search, ParameterType::STRING);
	    }
	    
	    	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'a.id');
	    $orderDirn = $this->state->get('list.direction', 'ASC');
	    
	    $ordering = [$db->quoteName('a.name') . ' ' . $db->escape($orderDirn), ];
	    	    
	    $query->order($ordering);
	    
	    return $query;
	}
	

	/**
	 * Method to get a list of proggrams.
	 * Overridden to add a check for access levels.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   4.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}
	
	

// 	    /**
// 	     * Method to change the published state of one or more records.
// 	     *
// 	     * @param   array    &$pks   A list of the primary keys to change.
// 	     * @param   integer  $value  The value of the published state.
// 	     *
// 	     * @return  boolean  True on success.
// 	     *
// 	     * @since   4.0.0
// 	     */
// 	    public function publish(&$pks, $value = 1) {
	        
// 	        $return = true;
// 	        foreach ($pks as $id) {
// 	            $rc = $this->processRegistration($id);
// 	            if (!$rc) {
// 	                $return = false;
// 	            }
// 	        }

// 	        return $return;
//      }
	        
	    
// 	    /**
// 	     * This function performs the business logic to process/publish a given registration.  This process will do the following:
// 	     * 
// 	     * 1.  Create a new team record if appropriate
// 	     * 2.  Add the record to the Team/Program/Division mapping file.
// 	     * 3.  Publish the registration record.  Once its published, it cannot be published again.
// 	     * 
// 	     * @param unknown $regid
// 	     * @return boolean
// 	     */
// 	    protected function processRegistration($regid) {

// 	        $db = $this->getDatabase();
	        
// 	        $rsvc = new RegistrationService();
// 	        $item = $rsvc->getItem($regid);

// 	        if ($item->published) {
// 	            Factory::getApplication()->enqueueMessage("State of a published registration cannot be changed", 'error');
// 	            return false;
// 	        }
// 	        try {
            

//                 // begin transactio
//                 $db->transactionStart();
    
//                 // =========================================================================================
//                 // Create Team record
//                 // =========================================================================================
//                 $query = $db->getQuery(true);
//                 $columns = array(
//                     'id',
//                     'name',
//                     'alias',
//                     'city',
//                     'state',
//                     'contactname',
//                     'contactemail',
//                     'contactphone',
//                     'published'
//                 );
//                 $teamname = OutputFilter::stringURLUnicodeSlug($item->teamname);
    
//                 $values = array(
//                     0,
//                     $db->quote($item->teamname), // teamname
//                     $db->quote($teamname), // alias
//                     $db->quote($item->city), // city
//                     $db->quote($item->state), // state
//                     $db->quote($item->name), // contact name
//                     $db->quote($item->email), // contact email
//                     $db->quote($item->phone), // contact phone
//                     1 // published
//                 );
    
//                 $query->insert($db->quoteName('#__jsports_teams'))
//                     ->columns($db->quoteName($columns))
//                     ->values(implode(',', $values));
    
//                 $db->setQuery($query);
//                 $db->execute();
    	           
//     	           // Get the row that was just inserted
//     	        $new_row_id = $db->insertid();
    	        
    	        
//     	        $query = $db->getQuery(true);
//                 // =========================================================================================
//     	        // Create Mapping Record - need newly created teamdid, programid, regid (set divid to 0)
//     	        // =========================================================================================
//     	        $columns = array(
//     	            'id',
//     	            'programid',
//     	            'teamid',
//     	            'divisionid',
//     	            'regid',
//     	            'published'
//     	        );
//     	        $values = array(
//     	            0,
//     	            $db->quote($item->programid), // programid (from registration record)
//     	            $db->quote($new_row_id), // team id - determined by previous insert into team table
//     	            $db->quote(0), // divisionid - set to zero (0) as divisional assignment has not been made yet
//     	            $db->quote($item->id), // Registration ID
//     	            0 // published - SET TO ZERO (0)
//     	        );
    	        
//     	        $query->insert($db->quoteName('#__jsports_map'))
//     	        ->columns($db->quoteName($columns))
//     	        ->values(implode(',', $values));
//     	        $db->setQuery($query);
//     	        $db->execute();
    	        
	        
//     	        // =========================================================================================
//     	        //  Set registration record to published.
//     	        // =========================================================================================
//     	        $query = $db->getQuery(true);
//     	        $fields = array($db->quoteName('published') . ' = 1');
//     	        $conditions = array($db->quoteName('id') . ' = ' .$item->id);
    	        
//     	        $query->update($db->quoteName('#__jsports_registrations'))
//     	        ->set($fields)
//     	        ->where($conditions);
//     	        $db->setQuery($query);
//     	        $db->execute();
    	        
//     	        // commit transaction
//     	        $db->transactionCommit();
	        
	        
// 	        } catch (Exception $e) {
// 	            // // catch any database errors.
// 	            $db->transactionRollback();
// 	            Factory::getApplication()->enqueueMessage("REgistration publish failed for one or more registration records", 'error');
// 	            return false;
	            
// 	        }
// 	        return true;
// 	    }

	    
	    /**
	     * Method to DELETE one or more records from the database
	     *
	     * @param   array    &$pks   A list of the primary keys to change.
	     * @param   integer  $value  The value of the published state.
	     *
	     * @return  boolean  True on success.
	     *
	     * @since   4.0.0
	     */
	    public function delete(&$pks, $value = 1) {
	        
	        
	        
	        /* this is a very simple method to change the state of each item selected */
	        $db = $this->getDatabase();
	        
	        $query = $db->getQuery(true);
	        
	        $query->delete($db->quoteName('#__jsports_registrations'))
	        ->whereIn($db->quoteName('id'), $pks);
	        $db->setQuery($query);
	        $db->execute();
	    }
	    
}