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

/**
 * Methods supporting a list of LEAGUE records.
 *
 * @since  1.0.0
 */
class RostersModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.0.0
	 * @see     \Joomla\CMS\MVC\Controller\BaseController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'programid', 'a.programid',
				'teamid', 'a.teamid',
			    'classification', 'a.classification',
			    'lastname', 'a.lastname',
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
	 * @since   1.0.0
	 */
	protected function populateState($ordering = 'a.id', $direction = 'asc')
	{
	    
	    $app = Factory::getApplication();
	    
	    $programid = $app->input->get('programid', 0, 'int');
	    if (empty($id)) {
	       $programid = $app->getUserState('com_jsports.programid');
	    }
	    $this->setState('programid', $programid);
	    
	    // keep the walk_id for adding new visits
	    $app->setUserState('com_jsports.programid', $programid);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
		
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
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.programid');
		$id .= ':' . $this->getState('filter.divisionid');
		$id .= ':' . $this->getState('filter.teamid');
		$id .= ':' . $this->getState('filter.classification');
		$id .= ':' . $this->getState('filter.lastname');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.0.0
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
	            'a.*, b.name  as teamname, c.divisionid'
	            )
	        );
	    $query->from($db->quoteName('#__jsports_rosters') . ' AS a,' .
               $db->quoteName('#__jsports_teams') . ' AS b, ' .
	       $db->quoteName('#__jsports_map') . ' AS c '
            );
	    
	    $conditions = array(
	        $db->quoteName('a.teamid') . ' = ' . $db->quoteName('b.id'),   // Join to the TEAMS table
	        $db->quoteName('a.programid') . ' = ' . $db->quoteName('c.programid'),   
	        $db->quoteName('a.teamid') . ' = ' . $db->quoteName('c.teamid'),   
	    );	    
	    $query->where($conditions);

        // Filter by PROGRAMID
        $programid = (string) $this->getState('filter.programid');
        if (is_numeric($programid))
        {
            $query->where($db->quoteName('a.programid') . ' = :programid');
            $query->bind(':programid', $programid, ParameterType::INTEGER);
        }

        // Filter by DIVISION ID
        $divisionid = (string) $this->getState('filter.divisionid');
        if (is_numeric($divisionid))
        {
            $query->where($db->quoteName('c.divisionid') . ' = :divisionid');
            $query->bind(':divisionid', $divisionid, ParameterType::INTEGER);
        }
        
        // Filter by TEAMID
        $teamid = (string) $this->getState('filter.teamid');
        if (is_numeric($teamid))
        {
            $query->where($db->quoteName('a.teamid') . ' = :teamid');
            $query->bind(':teamid', $teamid, ParameterType::INTEGER);
        }
        
        // Filter by CLASSIFICATION
        $classification = (string) $this->getState('filter.classification');
        if (is_numeric($classification))
        {
            $query->where($db->quoteName('a.classification') . ' = :classification');
            $query->bind(':classification', $classification, ParameterType::STRING);
        }
        
        
	    // Filter by search in name
	    $search = $this->getState('filter.search');
	    
	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where($db->quoteName('a.lastname') . ' LIKE :search')
	        ->bind(':search', $search, ParameterType::STRING);
	    }
	    
	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'a.id');
	    $orderDirn = $this->state->get('list.direction', 'ASC');
	    
	    $ordering = [$db->quoteName('a.lastname') . ' ' . $db->escape($orderDirn), ];
	    	    
	    $query->order($ordering);
	    
	    return $query;
	}
	

	/**
	 * Method to get a list of ROSTER RECORDS.
	 * Overridden to add a check for access levels.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.0.0
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
	        
// 	        /* this is a very simple method to change the state of each item selected */
// 	        $db = $this->getDatabase();
	        
// 	        $query = $db->getQuery(true);
	        
// 	        $query->update($db->quoteName('#__jsports_programs'))
// 	        ->set($db->quoteName('published') . ' = :value')
// 	        ->bind(':value', $value , ParameterType::INTEGER)
// 	        ->whereIn($db->quoteName('id'), $pks);
// 	        $db->setQuery($query);
// 	        $db->execute();
// 	    }
	
	
}