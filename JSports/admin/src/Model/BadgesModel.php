<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

/**
 * Methods supporting a list of Badges records.
 *
 */
class BadgesModel extends ListModel
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
				'programid', 'a.programid',
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
	    $teamid = $app->input->get('teamid', 0, 'int');
	    
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$programid = $this->getUserStateFromRequest($this->context . '.filter.programid', 'filter_programid', '');
		$this->setState('filter.programid', $programid);
	
		$teamid = $this->getUserStateFromRequest($this->context . '.filter.teamid', 'filter_teamid', '');
		$this->setState('filter.teamid', $teamid);
		
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
	            'a.*, p.name as programname, t.name as teamname'
	            )
	        );

// 	    $query->from($db->quoteName('#__jsports_teams') . ' AS a, ' .
// 	        $db->quoteName('#__jsports_map') . ' AS b ');
// 	    $query->where($db->quoteName('a.id') . ' = ' . $db->quoteName('b.teamid'));
// 	    $query->where ($db->quoteName('a.id') . ' in (select teamid from ' . $db->quotename('#__jsports_map')
// 	        . ' where programid = :programid )' );
	    $query->from($db->quoteName('#__jsports_team_badges') . ' AS a,' .
	        $db->quoteName('#__jsports_programs') . ' AS p,' .
	        $db->quoteName('#__jsports_teams') . ' AS t');
	    $query->where($db->quoteName('a.programid') . ' = ' . $db->quoteName('p.id'));
	    $query->where($db->quoteName('a.teamid') . ' = ' . $db->quoteName('t.id'));

// 	    echo $query;
// 	    exit;
	    
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
	    
	    
	    // Filter by search in date.
	    $city = $this->getState('filter.programid');
	    if (!empty($programid)) {
	        $query->where($db->quoteName('a.programid') . ' like :programid');
	        $query->bind(':programid', $programid, ParameterType::INTEGER);
	    }

	    // Filter by search in date.
	    $search = $this->getState('filter.search');
	    
	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where($db->quoteName('a.notes') . ' LIKE :search')
	        ->bind(':search', $search, ParameterType::STRING);
	    }
	    	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'a.programid');
	    $orderDirn = $this->state->get('list.direction', 'ASC');
	    
	    $ordering = [$db->quoteName('a.programid') . ' ' . $db->escape($orderDirn), ];
	    
	    
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
	        
	        $query->delete($db->quoteName('#__jsports_team_badges'))
	        ->whereIn($db->quoteName('id'), $pks);
	        $db->setQuery($query);
	        $db->execute();
	    }
	    
	
}