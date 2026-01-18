<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Services\ProgramsService;

/**
 * Methods supporting a list of Teams.
 *
 */
class GamesModel extends ListModel
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
	        
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$city = $this->getUserStateFromRequest($this->context . '.filter.city', 'filter_city', '');
		$this->setState('filter.city', $city);
			
		$programid = $this->getUserStateFromRequest($this->context . '.filter.programid', 'filter_programid',0);

		$viewtype = $this->getUserStateFromRequest($this->context . '.filter.viewtype', 'filter_viewtype', '');
		$this->setState('filter.viewtype', $viewtype);
		
		$teamid = $this->getUserStateFromRequest($this->context . '.filter.teamid', 'filter_teamid', 0);
		$this->setState('filter.teamid', $teamid);
			    
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
		$id .= ':' . $this->getState('filter.programid');
		$id .= ':' . $this->getState('filter.divisionid');
		$id .= ':' . $this->getState('filter.viewtype');
		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.  NOTE:  This function/query should not load anything PER DESIGN
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.6
	 */
	
	protected function getListQuery()
	{
	    
	    $db = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    $query->select('1')->from($db->quoteName('#__jsports_programs'))->where('1 = 0');
	    
	    return $query;
	}	    
	
	public function getItems() {

	    $programid     = (int) $this->getState('filter.programid');	
	    $divisionid    = (int) $this->getState('filter.divisionid') ?? 0;
	    $viewtype      = (string) $this->getState('filter.viewtype') ?? 'U';
	    $teamid        = (int) $this->getState('filter.teamid') ?? 0;

// 	    die('view type=' . $viewtype);
	    $limit = 100;
	    if ($viewtype === "U") {
	       return GameService::getUpcomingGamesByDivision($programid, $divisionid, $teamid);
	    } 
	    if ($viewtype === "C") {
	        return GameService::getCompletedGamesByDivision($programid, $divisionid, $teamid);
	    }
	    return GameService::getUpcomingGamesByDivision($programid, $divisionid, $teamid);
	}
}