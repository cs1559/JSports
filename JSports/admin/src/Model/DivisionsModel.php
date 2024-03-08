<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.0.0
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
 * Methods supporting a list of mywalks records.
 *
 */
class DivisionsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \Joomla\CMS\MVC\Controller\BaseController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name', 'a.name',
			    'programid', 'a.programid',
			    'ordering', 'a.ordering',
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
	 */
	protected function populateState($ordering = 'a.name', $direction = 'asc')
	{
	    
	    $app = Factory::getApplication();
	    
	    $programid = $app->input->get('programid', 0, 'int');
	    if (empty($id)) {
	       $programid = $app->getUserState('com_jsports.programid');
	    }
	    $this->setState('programid', $programid);
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
	            'a.*, b.name as program_name, b.description as program_description'
	            )
	        );
	    $query->from($db->quoteName('#__jsports_divisions') . ' AS a, ' . $db->quoteName('#__jsports_programs') . ' AS b ');
	    
	    $query->where($db->quoteName('a.programid') . ' = ' . $db->quoteName('b.id'));
	        
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
	    $status = $this->getState('filter.status');
	    if (!empty($status)) {	        
	        $query->where($db->quoteName('a.status') . ' = :status');
	        $query->bind(':status', $status, ParameterType::STRING);
	    }
	    
	    // Filter by search in date.
	    $search = $this->getState('filter.search');
	    
	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where($db->quoteName('a.name') . ' LIKE :search')
	        ->bind(':search', $search, ParameterType::STRING);
	    }
	    
	    // Filter by AGE GROUP
	    $agegroup = (string) $this->getState('filter.agegroup');
	    if (is_numeric($agegroup))
	    {
	        $query->where($db->quoteName('a.agegroup') . ' = :agegroup');
	        $query->bind(':agegroup', $agegroup, ParameterType::INTEGER);
	    }
	    
	    $programid = (string) $this->getState('filter.programid');
	    if (is_numeric($programid))
	    {
	        $query->where($db->quoteName('a.programid') . ' = :programid');
	        $query->bind(':programid', $programid, ParameterType::INTEGER);
	    }
	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'a.id');
	    $orderDirn = $this->state->get('list.direction', 'ASC');
	    
        $query->order($db->escape($orderCol . ' ' . $orderDirn));
	    return $query;
	}
	

	/**
	 * Method to get a list of proggrams.
	 * Overridden to add a check for access levels.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}
	
	

	    /**
	     * Method to change the published state of one or more records.
	     *
	     * @param   array    &$pks   A list of the primary keys to change.
	     * @param   integer  $value  The value of the published state.
	     *
	     * @return  boolean  True on success.
	     *
	     * @since   4.0.0
	     */
	    public function publish(&$pks, $value = 1) {
	        
	        /* this is a very simple method to change the state of each item selected */
	        $db = $this->getDatabase();
	        
	        $query = $db->getQuery(true);
	        
	        /* 2/12/2024 - added $db->quote to $value */
	        $query->update($db->quoteName('#__jsports_divisions'))
	        ->set($db->quoteName('published') . ' = :value')
	        ->bind(':value', $db->quote($value) , ParameterType::INTEGER)
	        ->whereIn($db->quoteName('id'), $pks);
	        $db->setQuery($query);
	        $db->execute();
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
	        
	        $query->delete($db->quoteName('#__jsports_divisions'))
	        ->whereIn($db->quoteName('id'), $pks);
	        $db->setQuery($query);
	        $db->execute();
	    }
	    	
}