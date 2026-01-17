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
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Administrator\Table\ProgramsTable;

/**
 * Methods supporting a list of ROSTER records.
 *
 * @since  1.0.0
 */
class RostersModel extends ListModel
{
    /**
     * @var TeamsTable $team 
     */
    protected $team;
    protected $teamlastyearplayed;
    
    /**
     * @var int $teamlastprogramid - last known programid that team participated in.
     */
    protected $teamlastprogramid;
    
    /**
     * 
     * @var ProgramsTable $program - last known program that team participated in.
     */
    protected $program;
    
    protected function populateState($ordering = null, $direction = null)
	{
	    parent::populateState($ordering, $direction);
	    
	    $app = Factory::getApplication();
	    $input = $app->input;
	    
// 	    $this->setState('rosters.id', $input->getInt('id'));
	    $this->setState('rosters.teamid', $input->getInt('teamid'));
	    $this->setState('rosters.programid', $input->getInt('programid'));
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
// 	    $id     = $input->getInt("teamid");
	    $id ??= (int) $this->getState('rosters.teamid', 0);
	    
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
	    $query->order("a.classification desc, a.lastname asc");
	    return $query;
	}
	
	public function getTeam() : TeamsTable {
	    return $this->team;
	}
	
	public function getProgram() : ProgramsTable {
	    return $this->program;
	}
		
}