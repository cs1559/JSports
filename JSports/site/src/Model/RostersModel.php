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

/**
 * CHANGELOG:
 * 2024-03-07  Changed the sort order to classificaiton desc, last name asc
 */
namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;

/**
 * Methods supporting a list of LEAGUE records.
 *
 * @since  1.0.0
 */
class RostersModel extends ListModel
{
    protected $team;
    protected $teamlastyearplayed;
    protected $teamlastprogramid;
    protected $program;
    
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
	    
	    $input = Factory::getApplication()->input;
	    $id     = $input->getInt("teamid");
	    
	    $svc = new TeamService();
	    $this->team = $svc->getItem($id);
	    $result = $svc->getMostRecentProgram($id);
	    if (isset($result['lastplayed'])) {
	       $this->teamlastyearplayed = $result['lastplayed'];
	    } else {
	       $this->teamlastyearplayed = null;
	    }
	    
	    if (isset($result['lastprogramid'])) {
// 	       $this->teamlastprogramid = $result['lastprogramid'];
	        $this->teamlastprogramid = $result['lastprogramid'];
	    } else {
	        $this->teamlastprogramid = null;
	    }
	    
	    
	    $psvc = new ProgramsService();
	    $this->program = $psvc->getItem($this->teamlastprogramid);
	    
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    // Select the required fields from the table.
	    $query->select(
	        $this->getState(
	            'list.select',
	            'a.*, b.name  as teamname'
	            )
	        );
	    $query->from($db->quoteName('#__jsports_rosters') . ' AS a,' .
                $db->quoteName('#__jsports_teams') . ' AS b '
            );
	    
	    $conditions = array(
	        $db->quoteName('a.teamid') . ' = ' . $db->quote($id),
	        $db->quoteName('a.programid') . ' = ' . $db->quote($this->teamlastprogramid),
	        $db->quoteName('a.teamid') . ' = ' . $db->quoteName('b.id'),   // Join to the TEAMS table
	    );	    
	    $query->where($conditions);
	    

        // Filter by PROGRAMID
        $programid = (string) $this->getState('filter.programid');
        if (is_numeric($programid))
        {
            $query->where($db->quoteName('a.programid') . ' = :programid');
            $query->bind(':programid', $programid, ParameterType::INTEGER);
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
	    	    
	    $query->order("a.classification desc, a.lastname asc");
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
	
		
	
}