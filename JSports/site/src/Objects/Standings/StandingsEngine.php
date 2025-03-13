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
namespace FP4P\Component\JSports\Site\Objects\Standings;

use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Table\StandingsTable;

class StandingsEngine
{
    
    protected $data;
    
    public function generateStandings($programid = null) {
        
        if (!$result = $this->flushDatabase()) {
            echo "ERROR:  Isseus flushing the standings database\n";
        }
        
        $result = $this->getData();
        echo "StandingsEngine:  # of records = " . count($result) ."<br/>\n";
        
        echo "StandingsEngine:  Loading Records  .... <br/>\n";
        foreach ($result as $item) {
            $this->loadRecord($item);
        }
        
        return true;
        
    }
    
    private function getData() {
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
         
        $sql = "
         select tempa.programid, tempa.divisionid, tempa.divname, tempa.teamid, tempa.teamname, tempa.headcoach, wins, losses, ties, (wins+losses+ties) gamesplayed, points, runsallowed, runsscored,
                (wins / (wins+ties+losses)) as winpct
        from (
select m.programid, m.divisionid, d.name as divname, teamid , t.name as teamname, t.contactname as headcoach, regid 
from #__jsports_map m, #__jsports_divisions d, #__jsports_teams t
where m.programid in (select id from #__jsports_programs where status <> 'C')
and m.divisionid = d.id
and m.teamid = t.id
and m.published = 1
) tempa
LEFT JOIN
(
select programid, id as teamid, teamname, sum(wins) wins, sum(losses) losses, sum(ties) ties, sum(points) points , sum(runs_allowed) runsallowed, sum(runs_scored) runsscored
from 
(
select  m.divisionid, hometeamid id, score.programid, team.name teamname, 
		sum(if(hometeamscore > awayteamscore,1,0)) wins, sum(if(hometeamscore < awayteamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(hometeampoints) points, sum( awayteamscore ) runs_allowed, sum( hometeamscore ) runs_scored,
	'homegame' game
from #__jsports_games score, #__jsports_teams team, #__jsports_map m  
where score.hometeamid = team.id 
		and score.hometeamid = m.teamid  and m.published = 1 
		and score.programid in (select id from #__jsports_programs where status <> 'C')
        and score.programid = m.programid
		and gamestatus = 'C' 
		and leaguegame = 1 	
group by programid, divisionid, id, team.name
UNION
select  m.divisionid, awayteamid id, score.programid, team.name teamname, 
		sum(if(awayteamscore > hometeamscore,1,0)) wins, sum(if(awayteamscore < hometeamscore,1,0)) losses, sum(if(hometeamscore = 			awayteamscore,1,0)) ties, sum(awayteampoints) points, sum( hometeamscore ) runs_allowed, sum( awayteamscore ) runs_scored,
	'awaygame' game
from #__jsports_games score, #__jsports_teams team, #__jsports_map m  
where score.awayteamid = team.id 
		and score.awayteamid = m.teamid  
		and m.published = 1 
		and score.programid  in (select id from #__jsports_programs where status <> 'C')
        and score.programid = m.programid
		and gamestatus = 'C' 
		and leaguegame = 1
group by programid, divisionid, id, team.name 
) temp
group by programid, teamid
) tempb
on tempa.teamid = tempb.teamid and tempa.programid = tempb.programid
";

        $db->setQuery($sql);
        
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return $db->loadAssocList();
        
               
    }
    
    private function loadRecord($item) {
        
        $db = Factory::getDbo();
        $table = new StandingsTable($db);
        $table->bind($item);
        $table->store();
        
    }
    
    /**
     * This function will clean out the standings database for active/pending programs.s
     */
    private function flushDatabase() {
        
        echo "StandingsEngine:  Flushing Standings Table - START". "<br/>\n";;
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        
        // delete all custom keys for user 1001.
//        $conditions = array(
  //          $db->quoteName('1') . ' like "1"',
    //    );
        
        $query->delete($db->quoteName('#__jsports_standings'));
        $query->where('1');
        
        $db->setQuery($query);
        
        $result = $db->execute();
        echo "StandingsEngine:  Result = " . $result . "<br/>\n";
        echo "StandingsEngine:  Flushing Standings Table - END". "<br/>\n";;
        return $result;
    }
    
}

