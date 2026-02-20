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

class Duplicateplayers implements Report
{
    private $name       = "Possible Duplicate Players";
    private $layout     = "reports.duplicateplayers"; 
    private $data;
    private $programid;
    private $format;
    private $filters;
        
    public function getName() {
        return $this->name;
    }
    
    public function getData() {
        
        /*
         * 
         * select d.name, d.agegroup, t.name, lastname, firstname,  soundex(concat(lastname,firstname)) as soundex 
from jos2823_jsports_rosters r, jos2823_jsports_teams t, jos2823_jsports_map m, jos2823_jsports_divisions d
where m.programid = 35
and r.programid = m.programid
and r.teamid = t.id
and t.id = m.teamid
and m.divisionid = d.id
order by soundex, lastname, firstname;

         */        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        $query = $db->getQuery(true);

/*
SELECT 
    d.name AS divisionname,
    d.agegroup,
    t.name AS teamname,
    r.lastname,
    r.firstname,
    SOUNDEX(CONCAT(r.lastname, r.firstname)) AS soundex
FROM xkrji_jsports_rosters r
INNER JOIN xkrji_jsports_teams t 
    ON r.teamid = t.id
INNER JOIN xkrji_jsports_map m 
    ON r.teamid = m.teamid 
    AND r.programid = m.programid
INNER JOIN xkrji_jsports_divisions d 
    ON m.divisionid = d.id
WHERE m.programid = 35
ORDER BY soundex, r.lastname, r.firstname;
*/
// Subquery to find soundex values that appear more than once
$subQuery = $db->getQuery(true);
$subQuery
    ->select('SOUNDEX(CONCAT(' . $db->quoteName('r.lastname') . ', ' . $db->quoteName('r.firstname') . ')) AS soundex')
    ->from($db->quoteName('#__jsports_rosters', 'r'))
    ->innerJoin(
        $db->quoteName('#__jsports_map', 'm') .
        ' ON ' . $db->quoteName('r.teamid') . ' = ' . $db->quoteName('m.teamid') .
        ' AND ' . $db->quoteName('r.programid') . ' = ' . $db->quoteName('m.programid')
    )
    ->innerJoin(
        $db->quoteName('#__jsports_teams', 't') .
        ' ON ' . $db->quoteName('r.teamid') . ' = ' . $db->quoteName('t.id')
    )
    ->innerJoin(
        $db->quoteName('#__jsports_divisions', 'd') .
        ' ON ' . $db->quoteName('m.divisionid') . ' = ' . $db->quoteName('d.id')
    )
    ->where($db->quoteName('m.programid') . ' = :programid')
    ->group($db->quoteName('soundex'))
    ->having('COUNT(*) > 1');

// Main query – get full rows only for duplicate soundex groups
$query
    ->select([
        $db->quoteName('d.name', 'divisionname'),
        $db->quoteName('d.agegroup'),
        $db->quoteName('t.name', 'teamname'),
        $db->quoteName('r.lastname'),
        $db->quoteName('r.firstname'),
        'SOUNDEX(CONCAT(' . $db->quoteName('r.lastname') . ', ' . $db->quoteName('r.firstname') . ')) AS ' . $db->quoteName('soundex')
    ])
    ->from($db->quoteName('#__jsports_rosters', 'r'))
    ->innerJoin(
        $db->quoteName('#__jsports_teams', 't') .
        ' ON ' . $db->quoteName('r.teamid') . ' = ' . $db->quoteName('t.id')
    )
    ->innerJoin(
        $db->quoteName('#__jsports_map', 'm') .
        ' ON ' . $db->quoteName('r.teamid') . ' = ' . $db->quoteName('m.teamid') .
        ' AND ' . $db->quoteName('r.programid') . ' = ' . $db->quoteName('m.programid')
    )
    ->innerJoin(
        $db->quoteName('#__jsports_divisions', 'd') .
        ' ON ' . $db->quoteName('m.divisionid') . ' = ' . $db->quoteName('d.id')
    )
    ->where($db->quoteName('m.programid') . ' = :programid')	
    ->where(
        'SOUNDEX(CONCAT(' . $db->quoteName('r.lastname') . ', ' . $db->quoteName('r.firstname') . ')) IN (' .
        $subQuery->__toString() .
        ')'
    )
    ->bind(':programid', $this->programid, ParameterType::INTEGER)
    ->order($db->quoteName('soundex') . ' ASC')
    ->order($db->quoteName('r.lastname') . ' ASC')
    ->order($db->quoteName('r.firstname') . ' ASC');



$db->setQuery($query);
        return $db->loadObjectList();
        
    }
    
    public function toHtml() {
        
        $rows = $this->getData();

        // Render from administrator component layouts folder
        return LayoutHelper::render($this->layout, $rows, JPATH_ADMINISTRATOR . '/components/com_jsports/layouts');
        
    }
 
    
    public function render()
    {
        
        switch ($this->format) {
            case 'html':
                return $this->toHtml();
                break;
                
            default:
                return $this->toHtml();
        }
        
    }
    public function setContext(array $context)
    {

        $this->filters      = $context['filters'];
        $this->programid    = isset($this->filters['programid']) ? $this->filters['programid'] : 0;
        $this->format       = isset($context['format']) ? $context['format'] : 0;
        
    }


    
}

