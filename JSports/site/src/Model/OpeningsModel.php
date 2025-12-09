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

namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;

/**
 * Methods supporting a list of mywalks records.
 *
 */
class OpeningsModel extends ListModel
{
    
    protected $programid;
    
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
			    'city', 'a.city',
			    'divisionname', 'c.name',
			    'contactname', 'a.contactname',
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
	protected function populateState($ordering = 'a.name', $direction = 'asc')
	{
	    
// 	    $app = Factory::getApplication();
	    
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$city = $this->getUserStateFromRequest($this->context . '.filter.city', 'filter_city', '');
		$this->setState('filter.city', $city);
			
		$programid = $this->getUserStateFromRequest($this->context . '.filter.programid', 'filter_programid', '');
		$this->setState('filter.programid', $programid);
		
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
		$id .= ':' . $this->getState('filter.city');
		$id .= ':' . $this->getState('filter.programid');
		$id .= ':' . $this->getState('filter.contactname');

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
	    
	    $programs = ProgramsService::getNonCompletedPrograms();
	    
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    // Select the required fields from the table.
	    $query->select(
	        $this->getState(
	            'list.select',
	            'a.*, b.programid, c.agegroup, c.name as divisionname, concat(a.name,a.contactname) as searchfield'
	            )
	        );

	   
	    $programid = (string) $this->getState('filter.programid');	    
	    $divisionid = (string) $this->getState('filter.divisionid');
	    
        if (strlen($programid) < 1) {
 	        $programid = 0;
 	        $divisionid = 0;
	    }
	    
// 	    if (is_numeric($programid)) {
	        $query->from($db->quoteName('#__jsports_teams') . ' AS a, ' .
	            $db->quoteName('#__jsports_map') . ' AS b,  '  .
	            $db->quoteName('#__jsports_divisions') . ' AS c ' );
	        $query->where($db->quoteName('b.programid') . ' in (select id from #__jsports_programs where status <> "C" and published = 1) ');
	        $query->where($db->quoteName('a.openroster') . ' = 1' );
	        $query->where($db->quoteName('a.id') . ' = ' . $db->quoteName('b.teamid'));
	        $query->where($db->quoteName('b.divisionid') . ' = ' . $db->quoteName('c.id'));
	        
	        
// 	    } else {
// 	        $query->from($db->quoteName('#__jsports_teams') . ' AS a, ' .
// 	            $db->quoteName('#__jsports_map') . ' AS b,  ' .
// 	            $db->quoteName('#__jsports_divisions') . ' AS c '
// 	            );
// 	        $query->where($db->quoteName('a.id') . ' = ' . $db->quoteName('b.teamid'));
// 	    }
	    
	    // Filter by search in date.
	    $search = $this->getState('filter.search');
	    
// 	    $programid = (string) $this->getState('filter.programid');
//         $programid = abs((int) $programid);
// 	    if (is_numeric($programid))
// 	    {
// 	        $query->where($db->quoteName('b.programid') . ' = :programid');
// 	        $query->bind(':programid', $programid, ParameterType::INTEGER);
// 	    }

// 	    $divisionid = (string) $this->getState('filter.divisionid');
// 	    if (is_numeric($divisionid))
// 	    {
// 	        $query->where($db->quoteName('b.divisionid') . ' = :divisionid');
// 	        $query->bind(':divisionid', $divisionid, ParameterType::INTEGER);
// 	    }
	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'a.name');
	    $orderDirn = $this->state->get('list.direction', 'asc');
	    
	    $query->order($db->escape($orderCol . ' ' . $orderDirn));
	    
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
		return parent::getItems();

		
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
	        
	        $query->update($db->quoteName('#__jsports_teams'))
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
	    public function delete(&$pks, $value = 1) {
	        
	        /* this is a very simple method to change the state of each item selected */
	        $db = $this->getDatabase();
	        
	        $query = $db->getQuery(true);
	        
	        $query->delete($db->quoteName('#__jsports_teams'))
	        ->whereIn($db->quoteName('id'), $pks);
	        $db->setQuery($query);
	        $db->execute();
	    }
	    
	
}