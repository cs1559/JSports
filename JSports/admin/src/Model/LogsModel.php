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

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

/**
 * Methods supporting a list of mywalks records.
 *
 */
class LogsModel extends ListModel
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
				'name', 'a.username',
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
	    
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.username', 'filter_username', '');
		$this->setState('filter.username', $published);
		
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
		$id .= ':' . $this->getState('filter.username');

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

	    $query->from($db->quoteName('#__jsports_action_logs') . ' AS a');	    
	    
	    // Filter by search in date.
	    $username = $this->getState('filter.username');
	    if (!empty($username)) {
	        $search = '%' . trim($username) . '%';
	        $query->where($db->quoteName('a.username') . ' like :username');
	        $query->bind(':username', $status, ParameterType::STRING);
	    }

	    // Filter by search in date.
	    $search = $this->getState('filter.search');
	    
	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where($db->quoteName('a.msg') . ' LIKE :search')
	        ->bind(':search', $search, ParameterType::STRING);
	    }
	    	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'a.logdate');
	    $orderDirn = $this->state->get('list.direction', 'DESC');
	    
	    $ordering = [$db->quoteName($orderCol) . ' ' . $db->escape($orderDirn), ];
	    
	    
	    $query->order($ordering);
	    return $query;
	}
	

	/**
	 * Method to get a list of action logs.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   4.0.0
	 */
	public function getItems()
	{
		return parent::getItems();

		
	}
	
	
    
}