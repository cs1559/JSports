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

use Joomla\CMS\MVC\Model\FormModel;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Services\RosterService;
use FP4P\Component\JSports\Site\Services\StandingsService;
use FP4P\Component\JSports\Administrator\Services\SecurityService;

use FP4P\Component\JSports\Site\Services\UserService;

/**
 * Methods supporting a list of mywalks records.
 *
 * @since  1.6
 */
class TeamModel extends FormModel
{
    
    /**
     * @var     object  The user profile data.
     * @since   1.6
     */
    protected $data;
    
    protected $programs;
    protected $recordhistory;
    protected $recentprogramid;
    protected $recentprogram;
    protected $rosterstaff;
    protected $rosterplayers;
    protected $games;
    protected $standings;
    protected $canSeeRoster = false;

    
    protected $form = 'team';
    
    
    public function getData(){
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new TeamService();
        $item = $svc->getItem($id);
        
        
        $this->recordhistory  = $this->getTeamStatsByProgram($id);
        $result = $svc->getMostRecentProgram($id);
        $recentprogramid = $result['lastprogramid'];
        
        // Get team division
        $divisionid = TeamService::getTeamDivisionId($id, $recentprogramid);
        
        $psvc = new ProgramsService();
        $this->recentprogram = $psvc->getItem($recentprogramid);
        
        
        $rsvc = new RosterService();
        $this->rosterstaff = $rsvc->getRosterStaff($id, $recentprogramid);
        
        if (SecurityService::canViewTeamRoster($id, $divisionid)) {
            $this->canSeeRoster = true;
            $this->rosterplayers = $rsvc->getRosterPlayers($id, $recentprogramid);
        }else {
            $this->rosterplayers = null;
        }

        $this->standings = StandingsService::getProgramStandings(
            $this->recentprogram->id, false, $divisionid);
        
        $gsvc = new GameService();
        $this->games = $gsvc->getTeamSchedule($id, $recentprogramid);
        
        return $item;
    }
    
    
    public function getItem(){

        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new TeamService();
        $item = $svc->getItem($id);

        return $item;
    }
        
    
    public function getForm($data = array(), $loadData = true)
    {
        
        $form = $this->loadForm('com_jsports.team', 'team', ['control' => 'jform', 'load_data' => true]);
        
        $form = $this->loadForm(
            'com_jsports_form.team.data', // just a unique name to identify the form
            'team',				     // the filename of the XML form definition
            array(
                'control' => 'jform',	// the name of the array for the POST parameters
                'load_data' => $loadData	// will be TRUE
            )
            );
                
        if (empty($form))
        {
            $errors = $this->getErrors();
            throw new Exception(implode("\n", $errors), 500);
        }
        
        return $form;
    }
    
    protected function loadFormData()
    {
        
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_jsports_form.team.data',	// a unique name to identify the data in the session
                array($this->data)	// prefill data if no data found in session
            );
        
        if (empty($data)) {
            $data = $this->getItem();
        }        
        
        $this->preprocessData('jsports.team', $data);
        
        
        return $data;
    }
    
    
    /**
     * This function will save/store the data captured on the Registration EDIT form and save it to the database.
     *
     * @param unknown $data
     * @return unknown
     */
    public function save($data) {
        
        $team = TeamService::getTeamsTable();

	$team->bind($data);
	$team->check();

	// Block of code to prevent an "incorrect integer value" own the ownerid field.
	if (strlen($data["ownerid"]) < 1) {	
		$data["ownerid"] = 0;
	}
        
        //@TODO Need to add code to catch any error that may exist.
	if ($team->save($data)) {
		return true;
	} else {
	    $errors = $team->getErrors();
	    $this->setError($errors[0]);
		$app = Factory::getApplication();
		$app->enqueueMessage($errors[0],'error');
		return false;
	}

    	return true;        
    }
    
    
    
    public function getTeamPrograms($teamid) {
                
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        
        // Create a new query object.
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select($db->quoteName(array('a.id', 'a.name')));
        
        $query->from($db->quoteName('#__jsports_programs') . ' AS a, ' . $db->quoteName('#__jsports_map') . ' AS b ');
        
        $query->where($db->quoteName('a.id') . ' = ' . $db->quoteName('b.programid'));
        
        $query->where($db->quoteName('b.teamid') . ' = ' . $db->quote($teamid));
        $query->order('a.name ');
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectList();
        
        
        return $results;
    }
    
    public function getTeamStatsByProgram($teamid) {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        
        // Create a new query object.
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
//         $query->select($db->quoteName(array('a.programid', 'a.teamid', 'a.programname', 'a.divisionname', 'a.teamname', 'a.runsscored',
//             'runsallowed', 'wins','losses','ties','points')));
        
//         $query->from($db->quoteName('#__jsports_recordhistory') . ' AS a ');
        
//         $query->where($db->quoteName('a.teamid') . ' = ' . $db->quote($teamid));
//         $query->order('a.programname DESC');

        $query = " select * from (
            select s.programid, p.name as programname, d.name as divisionname , teamid, teamname, wins, losses, ties, points, runsscored, runsallowed
            from #__jsports_standings s, #__jsports_programs p, #__jsports_divisions d
            where teamid = " . $teamid . "
            and s.programid = p.id
            and s.divisionid = d.id
            UNION
            select programid, programname, divisionname, teamid, teamname, wins, losses, ties, points, runsscored, runsallowed
            from #__jsports_recordhistory
            where teamid = " . $teamid . " ) temp
            order by programname DESC
    ";
        
        
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $results = $db->loadObjectList();
        
        return $results;
    }

    
    
}