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

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class Gamescores extends AbstractReport
{

    private $programid;
        
    public function __construct() {
        $this->setName("Game Scores Report");
        $this->setLayout("reports.gamescores");
    }

    
    public function getData() : array {
        
        /*
select g.id, g.name, g.gamedate, g.gametime, d.name as 'divisionname', g.hometeamname, g.hometeamscore,g.awayteamname, g.awayteamscore, m1.divisionid as homedivision, m2.divisionid as awaydivision,     CASE
        WHEN m1.divisionid <> m2.divisionid THEN 'YES'
        ELSE 'NO'
    END AS crossdivisiongame
from xkrji_jsports_games as g, xkrji_jsports_divisions as d, xkrji_jsports_map as m1, xkrji_jsports_map as m2
where g.programid = 35
and g.gamestatus = 'C'
and g.divisionid = d.id
and g.hometeamid = m1.teamid
and m1.programid = 35
and g.awayteamid = m2.teamid
and m2.programid = 35

         */        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        $query = $db->getQuery(true);

        $query
        ->select([
            $db->quoteName('g.id'),
            $db->quoteName('g.name'),
            $db->quoteName('g.gamedate'),
            $db->quoteName('g.gametime'),
            $db->quoteName('d.agegroup'),
            $db->quoteName('d.name', 'divisionname'),
            $db->quoteName('g.hometeamname'),
            $db->quoteName('g.hometeamscore'),
            $db->quoteName('g.awayteamname'),
            $db->quoteName('g.awayteamscore'),
            $db->quoteName('m1.divisionid', 'homedivision'),
            $db->quoteName('m2.divisionid', 'awaydivision'),
            "CASE
            WHEN " . $db->quoteName('m1.divisionid') . " <> " . $db->quoteName('m2.divisionid') . " THEN 'YES'
            ELSE 'NO'
         END AS " . $db->quoteName('crossdivisiongame'),
        ])
        ->from($db->quoteName('#__jsports_games', 'g'))
        ->innerJoin(
            $db->quoteName('#__jsports_divisions', 'd') .
            ' ON ' . $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id')
            )
            ->innerJoin(
                $db->quoteName('#__jsports_map', 'm1') .
                ' ON ' . $db->quoteName('g.hometeamid') . ' = ' . $db->quoteName('m1.teamid') .
                ' AND ' . $db->quoteName('m1.programid') . ' = :program1'
                )
                ->innerJoin(
                    $db->quoteName('#__jsports_map', 'm2') .
                    ' ON ' . $db->quoteName('g.awayteamid') . ' = ' . $db->quoteName('m2.teamid') .
                    ' AND ' . $db->quoteName('m2.programid') . ' = :program2'
                    )
                    ->where($db->quoteName('g.programid') . ' = :program')
                    // gamestatus inline (not bound)
        ->where($db->quoteName('g.gamestatus') . ' = ' . $db->quote('C'))
        ->where($db->quoteName('g.leaguegame') . ' = 1')
        ->order([
            $db->quoteName('d.ordering') . ' ASC',
            $db->quoteName('g.gamedate') . ' ASC',
            $db->quoteName('g.gametime') . ' ASC',
        ]);
        
        $query->bind(':program',  $this->programid, ParameterType::INTEGER);
        $query->bind(':program1', $this->programid, ParameterType::INTEGER);
        $query->bind(':program2', $this->programid, ParameterType::INTEGER);
                   
        $db->setQuery($query);
        return $db->loadObjectList();
        
    }
    

    
    public function setContext(array $context) : void
    {

        $this->filters      = $context['filters'];
        $this->programid    = isset($this->filters['programid']) ? $this->filters['programid'] : 0;

        $this->format       = isset($context['format']) ? $context['format'] : 0;
        
    }


    
}

