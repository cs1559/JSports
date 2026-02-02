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
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Administrator\Table\ProgramsTable;

/**
 * Methods supporting displaying a list of games on a teams schedule.
 *
 * @since  1.0.0
 */
class SchedulesModel extends ListModel
{
    /** 
     * @var TeamsTable $team
     */
    protected $team;
//     protected $teamlastyearplayed;
    
    /**
     * @var int
     */
    protected $programid;
    
    /**
     * @var ProgramsTable
     */
    protected $program;
    
    /**
     * Return the current team object
     * @return TeamsTable|NULL
     */
    public function getTeam() : ?TeamsTable 
    {
        return $this->team;
    }
    
    /**
     * return the current progrma table.
     * @return ProgramsTable
     */
    public function getProgram() : ?ProgramsTable
    {
        return $this->program;
    }
    
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState($ordering, $direction);
        
        $app = Factory::getApplication();
        $input = $app->input;

        $this->setState('schedules.teamid', $input->getInt('teamid'));
        $this->setState('schedules.programid', $input->getInt('programid'));
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
	    
	    // Get Team Id
	    $teamid ??= (int) $this->getState('schedules.teamid', 0);
	    $programid ??= (int) $this->getState('schedules.programid', 0);
// 	    $teamid     = $input->getInt("teamid");
// 	    $programid     = $input->getInt("programid");
	    
	    $svc = new TeamService();
	    $this->team = $svc->getItem($teamid);
	    $result = $svc->getMostRecentProgram($teamid);

	    if (is_null($programid) || $programid == 0) {
    	    if (isset($result['lastprogramid'])) {
    	        $this->programid = $result['lastprogramid'];
    	    } else {
    	        $this->programid = null;
    	    }
	    } else {
	        $this->programid = $programid;
	    }

	    $psvc = new ProgramsService();
	    $this->program = $psvc->getItem($this->programid);
	    
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    $query->select("a.*");
	    $query->from($db->quoteName('#__jsports_games') . ' AS a')
  	       ->where($db->quoteName('a.programid') . ' = :programid' )
            ->where(" (" . $db->quoteName('a.teamid') . ' = :teamid or ' 
                . $db->quoteName('a.opponentid') . ' = :opponentid )')
            ->order("gamedate asc");
	    
        $query->bind(':programid', $this->programid, ParameterType::INTEGER);
        $query->bind(':teamid', $teamid, ParameterType::INTEGER);
        $query->bind(':opponentid', $teamid, ParameterType::INTEGER);
           
	    return $query;
	}
	
}