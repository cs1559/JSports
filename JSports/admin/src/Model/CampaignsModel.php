<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
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
class CampaignsModel extends ListModel
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
				'sponsorid', 'a.sponsorid',
				'position', 'a.positionid',
			    'campaigntype', 'a.campaigntype',
			    'published', 'a.published',
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

// 		$grouping = $this->getUserStateFromRequest($this->context . '.filter.grouping', 'filter_grouping', '');
// 		$this->setState('filter.grouping', $grouping);
		
// 		$existingteam = $this->getUserStateFromRequest($this->context . '.filter.existingteam', 'filter_existingteam', '');
// 		$this->setState('filter.existingteam', $existingteam);
		
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
// 		$id .= ':' . $this->getState('filter.programid');
		$id .= ':' . $this->getState('filter.sponsorid');
		$id .= ':' . $this->getState('filter.positionid');
		$id .= ':' . $this->getState('filter.published');

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
	            'a.*, s.name as sponsorname, sp.plancode, p.name as planname'
	            )
	        );
	    $query
	    ->from($db->quoteName('#__jsports_campaigns', 'a') . ', ' .
	           $db->quoteName('#__jsports_sponsors', 's') . ', ' .
	           $db->quoteName('#__jsports_sponsorship_plans', 'p') . ', ' .
	           $db->quoteName('#__jsports_sponsorships', 'sp') 
	        )->where(
	            $db->quoteName('a.sponsorid')
	            . ' = ' .
	            $db->quoteName('s.id')
	            );
	    $query->where($db->quoteName('a.sponsorshipid') . ' = ' . $db->quoteName('sp.id'));
	    $query->where($db->quoteName('sp.plancode') . ' = ' . $db->quoteName('p.plancode'));
	        
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
	    
	    // Filter by Sponsor.
	    $sponsorid = $this->getState('filter.sponsorid');
	    if (!empty($sponsorid)) {	        
	        $query->where($db->quoteName('a.sponsorid') . ' = :sponsorid');
	        $query->bind(':sponsorid', $sponsorid, ParameterType::INTEGER);
	    }

	    // Filter by search in POSITION.
	    $position = $this->getState('filter.position');
	    
	    if (!empty($position))
	    {
	        $position = '%' . trim($position) . '%';
	        $query->where($db->quoteName('a.positions') . ' LIKE :position');
	        $query->bind(':position', $position, ParameterType::STRING);
	    }
	    
	    
	    // Filter by search in SPONSORNAME.
	    $search = $this->getState('filter.search');
	    
	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where($db->quoteName('s.sponsorname') . ' LIKE :search');
	        $query->bind(':search', $search, ParameterType::STRING);
	    }
	    
	    	    
	    // Add the list ordering clause.
	    $orderCol  = $this->state->get('list.ordering', 'a.id');
	    $orderDirn = $this->state->get('list.direction', 'ASC');
	    
	    $ordering = [$db->quoteName('sponsorname') . ' ' . $db->escape($orderDirn), ];
	    	    
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
		
}