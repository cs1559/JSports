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
namespace FP4P\Component\JSports\Site\Objects\Reports;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class NoRosterReport extends AbstractReport
{

    private $programid;
    
    public function __construct() {
        $this->setName("No Roster Report");
        $this->setLayout("reports.noroster");
    }
    
    public function getData() {
        
        /*
         * select t.id, t.name as 'team_name', t.contactname, d.agegroup, d.name as 'division_name'
from jos2823_jsports_teams t, jos2823_jsports_map m, jos2823_jsports_divisions d
where t.id= m.teamid
and m.divisionid = d.id
and m.programid = 35
    and m.published = 1
and t.id not in (
select teamid
from jos2823_jsports_rosters
where programid = 35
and classification = 'P'
group by teamid
    )
order by a.agegroup, d.name, t.name
         */        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
//         $programId = 37;
        
        // --- subquery: teams that DO have a 'P' classification roster in this program
        $subQuery = $db->getQuery(true)
        ->select($db->quoteName('teamid'))
        ->from($db->quoteName('#__jsports_rosters'))
        ->where($db->quoteName('programid') . ' = :pid2')
        ->where($db->quoteName('classification') . ' = ' . $db->quote('P'))
        ->group($db->quoteName('teamid'));
        
        // --- main query
        $query = $db->getQuery(true)
        ->select([
            $db->quoteName('t.id','teamid'),
            $db->quoteName('t.name', 'teamname'),
            $db->quoteName('t.contactname','contactname'),
            $db->quoteName('d.agegroup','agegroup'),
            $db->quoteName('d.name', 'divisionname'),
        ])
        ->from($db->quoteName('#__jsports_teams', 't'))
        ->join('INNER', $db->quoteName('#__jsports_map', 'm') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('m.teamid'))
        ->join('INNER', $db->quoteName('#__jsports_divisions', 'd') . ' ON ' . $db->quoteName('m.divisionid') . ' = ' . $db->quoteName('d.id'))
        ->where($db->quoteName('m.programid') . ' = :pid')
        ->where($db->quoteName('m.published') . ' = 1')
        ->where($db->quoteName('t.id') . ' NOT IN (' . $subQuery . ')')
        ->order([
            $db->quoteName('d.agegroup'),
            $db->quoteName('d.name'),
            $db->quoteName('t.name'),
        ]);
        
        $query->bind(':pid', $this->programid, ParameterType::INTEGER);
        $query->bind(':pid2', $this->programid, ParameterType::INTEGER);
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    public function setContext(array $context)
    {

        $this->filters      = $context['filters'];
        $this->programid    = isset($this->filters['programid']) ? $this->filters['programid'] : 0;
        $this->format       = isset($context['format']) ? $context['format'] : 0;
        
    }


    
}

