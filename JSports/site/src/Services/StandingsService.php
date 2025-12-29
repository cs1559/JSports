<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Services;

use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;

class StandingsService
{

    public static function getProgramStandings($programid, $past = false, $divid = 0)
    {
        
        $program = ProgramsService::getItem($programid);
        
        if ($past) {
            $table = '#__jsports_past_standings';
        } else {
            $table = '#__jsports_standings';
        }
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // $query->select('a.*, (wins/(losses+wins)) as winpct, d.name as divisionname, d.ordering');
        $query->select('a.*,d.name as divisionname, d.ordering');
        $query->from($db->quoteName($table) . ' AS a, ' . $db->quoteName('#__jsports_divisions') . ' AS d');
        $conditions = array(
            $db->quoteName('a.divisionid') . ' = ' . $db->quoteName('d.id'),
            $db->quoteName('a.programid') . ' = ' . $db->quote($programid)
        );

        if ($divid > 0) {
            $conditions[] = $db->quoteName('a.divisionid') . ' = ' . $db->quote($divid);
        }
        $query->where($conditions);
        
        switch ($program->standingspolicy) {
            case 'POINTS':
                $query->order('d.ordering, d.name, points desc, runsallowed asc, 
                        runsscored desc, gamesplayed desc, teamid');
                break;
            case 'WINPCT':
                $query->order('d.ordering, d.name, winpct desc, runsallowed asc, 
                        runsscored desc, gamesplayed desc, teamid');
                break;
            default:
                $query->order('d.ordering, d.name, points desc, runsallowed asc, 
                        runsscored desc, gamesplayed desc, teamid');
                break;
        }


        // $query->where($conditions);
        $db->setQuery($query);

        return $db->loadAssocList();
    }

    /**
     * 
     * @param int $teamid
     * @param int $programid
     * @param int $divisionid
     * @return array
     */
    public static function getTeamList($teamid, $programid, $divisionid = null)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        if (is_null($divisionid)) {
            $sql = "SELECT a.id as teamid, a.name as teamname, m.divisionid, d.name as divisionname, 
                        d.agegroup FROM " . $db->quoteName('#__jsports_teams') . " as a, " . 
                        $db->quoteName('#__jsports_map') . " as m, " . 
                        $db->quoteName('#__jsports_divisions') . " as d
                where m.teamid = a.id
                and m.divisionid = d.id
                and m.divisionid in (
                    select divisionid from #__jsports_map as m, #__jsports_divisions as d
                    where m.divisionid = d.id
                    and m.teamid = " . $db->quote($teamid) . " and m.programid = " . $db->quote($programid) . "
                    )";
        }

        $query->setQuery($sql);
        // $query->bind(':teamid', $teamid, ParameterType::INTEGER);
        // $query->bind(':programid', $programid, ParameterType::INTEGER);
        $db->setQuery($query);

        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        return $db->loadAssocList();
    }
}
    
