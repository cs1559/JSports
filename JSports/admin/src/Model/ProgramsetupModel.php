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
 * Methods supporting a list pf JSPROTS MAP RECORDS
 *
 * @since  1.6
 */
class ProgramsetupModel extends ListModel
{
    
    protected $programid = 0;
    protected $programname = '';
    
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
		$app = Factory::getApplication();
		$programid = $app->input->get('programid', 0, 'int');
		$this->programid = $programid;
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
	protected function populateState($ordering = 'c.name', $direction = 'asc')
	{
	    
	    $app = Factory::getApplication();
	    
	    $programid = $app->input->get('programid', 0, 'int');
	    if (empty($programid)) {
	       $programid = $app->getUserState('com_jsports.programid');
	    }

	    $this->setState('filter.programid', $programid);
	    $app->setUserState('com_jsports.programid', $programid);
	    
		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$grouping = $this->getUserStateFromRequest($this->context . '.filter.grouping', 'filter_grouping', '');
		$this->setState('filter.grouping', $grouping);
		
		// List state information.
		parent::populateState($ordering, $direction);
	}

	
	public function getForm($data = array(), $loadData = true)
	{
	    // Get the form.
	    $form = $this->loadForm('com_jsports.programsetup', 'programsetup', array('control' => 'jform', 'load_data' => $loadData));
	    
	    if (empty($form))
	    {
	        return false;
	    }
	    
	    return $form;
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
	protected function getStoreId($id = 'programsetup')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.programid');
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
// 	    $programid = $this->getState('filter.programid');
	    
	    $app = Factory::getApplication();
	    $programid = $app->input->get('programid', 0, 'int');
	    
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    // Select the required fields from the table.
	    $query->select(
	        $this->getState(
	            'list.select',
	            'a.*, b.name as programname, c.name as teamname, r.grouping, r.name as contactname'
	            )
	        );
	    $query->from($db->quoteName('#__jsports_map') . ' AS a, ' 
	           . $db->quoteName('#__jsports_programs') . ' AS b, '
	           . $db->quoteName('#__jsports_registrations') . ' AS r, '
	           . $db->quoteName('#__jsports_teams') . 'AS c');
	    
	    
	    $conditions = array(
	        $db->quoteName('a.programid') . ' = ' . $db->quoteName('b.id'),    // Join to the PROGRAMS table
	        $db->quoteName('a.teamid') . ' = ' . $db->quoteName('c.id'),       // Join to the TEAMS table
	        $db->quoteName('a.regid') . ' = ' . $db->quoteName('r.id'),        // Join to the REGISTRATIONS table
	        $db->quoteName('b.setupfinal') . ' = 0 ' ,      // Prevent someone from altering setup after its been finalized
	        $db->quoteName('b.registrationonly') . ' = 0 ' ,       // Eliminate registration only programs as this does not apply
	    );
	    
	    $query->where($conditions);
	    
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
 	    
 	    //echo "PROGRAM ID = " . $programid;
	    if (!empty($programid)) {	        
	        $query->where($db->quoteName('a.programid') . ' = :programid');
	        $query->bind(':programid', $programid, ParameterType::INTEGER);
	    }

	    // Filter by AGE GROUP
	    $grouping = (string) $this->getState('filter.grouping');
	    if (!empty($grouping))
	    {
	        $_group = $grouping . '%';
	        $query->where($db->quoteName('r.grouping') . ' like :grouping');
	        $query->bind(':grouping', $_group, ParameterType::STRING);
	    }
	    
	    // Filter by search in TEAMNAME.
	    $search = $this->getState('filter.search');
	    
	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where($db->quoteName('c.name') . ' LIKE :search')
	        ->bind(':search', $search, ParameterType::STRING);
	    }
	    
	    	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'r.grouping');
	    $orderDirn = $this->state->get('list.direction', 'DESC');
	    
	    $ordering = [$db->quoteName('r.grouping') . ' ' . $db->escape($orderDirn), ];
	    	    
	    $query->order($db->escape($orderCol . ' ' . $orderDirn));
	    
	    //$query->order($ordering);
	    
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
	        $query->update($db->quoteName('#__jsports_map'))
	        ->set($db->quoteName('published') . ' = :value')
	        ->bind(':value', $value , ParameterType::INTEGER)
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
	    public function assigndivisions(&$pks, $value = 1) {

	        
	        echo "inside assigndivisions";
	        exit;
	        
	        
// 	        /* this is a very simple method to change the state of each item selected */
// 	        $db = $this->getDatabase();
	        
// 	        $query = $db->getQuery(true);
	        
// 	        $query->delete($db->quoteName('#__jsports_map'))
// 	        ->whereIn($db->quoteName('id'), $pks);
// 	        $db->setQuery($query);
// 	        $db->execute();
	    }

	    
	    public function delete(&$pks, $value = 1) {
	        
	        
	        
	    }
}