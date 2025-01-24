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

/**
 * CHANGE HISTORY
 * 7/10/2024 - Changed AGE GROUP to GROUPING; commented out Delete function.
 * 
 */

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
//use FP4P\Component\JSports\Administrator\Services\RegistrationService;
use FP4P\Component\JSports\Site\Services\RegistrationService;
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
			    'grouping', 'a.grouping',
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
	    $programid = $app->input->get('id', 0, 'int');
	    if (empty($programid)) {
	       $programid = $app->getUserState('com_jsports.programid');
	    }
	    $this->setState('programid', $programid);
	    $app->setUserState('com_jsports.programid', $programid);
	    
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$grouping = $this->getUserStateFromRequest($this->context . '.filter.grouping', 'filter_grouping', '');
		$this->setState('filter.grouping', $grouping);
		
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
// 		$id .= ':' . $this->getState('filter.programid');
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.grouping');

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
// 	    $programid = $this->getState('filter.programid');
	    $programid = $this->getState('filter.id');
	    if (!empty($programid)) {	        
	        $query->where($db->quoteName('a.programid') . ' = :programid');
	        $query->bind(':programid', $programid, ParameterType::INTEGER);
	    }

	    // Filter by AGE GROUP
	    $grouping = (string) $this->getState('filter.grouping');
	    
	    if (strlen($grouping)>0) {
	       $query->where($db->quoteName('a.grouping') . ' = :grouping');
	       $query->bind(':grouping', $grouping, ParameterType::STRING);
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
// 	     * Method to DELETE one or more records from the database
// 	     *
// 	     * @param   array    &$pks   A list of the primary keys to change.
// 	     * @param   integer  $value  The value of the published state.
// 	     *
// 	     * @return  boolean  True on success.
// 	     *
// 	     * @since   4.0.0
// 	     */
// 	    public function delete(&$pks, $value = 1) {
	        
	        
	        
// 	        /* this is a very simple method to change the state of each item selected */
// 	        $db = $this->getDatabase();
	        
// 	        $query = $db->getQuery(true);
	        
// 	        $query->delete($db->quoteName('#__jsports_registrations'))
// 	        ->whereIn($db->quoteName('id'), $pks);
	        
// 	        $db->setQuery($query);
	        
// 	        $db->execute();
	        
	        
	        
// 	    }
	    
}