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

class RunDifferential extends AbstractReport
{

    private $programid;
        
    public function __construct() {
        $this->setName("Run Differential");
        $this->setLayout("reports.rundifferential");
    }

    
    public function getData() {
        
        /*

SELECT divisionid, d.name, avg(Abs(hometeamscore - awayteamscore)) as diff 
FROM `jos2823_jsports_games` g, jos2823_jsports_divisions d
where g.programid = 35
and g.gamestatus = 'C'
and g.divisionid = d.id
group by divisionid
order by d.ordering

         */        
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        
        $query = $db->getQuery(true);
        
        $query
        ->select([
            $db->quoteName('g.divisionid'),
            $db->quoteName('d.name'),
            // avg(abs(hometeamscore - awayteamscore)) as diff
            'AVG(ABS(' . $db->quoteName('g.hometeamscore') . ' - ' . $db->quoteName('g.awayteamscore') . ')) AS ' . $db->quoteName('diff'),
        ])
        ->from($db->quoteName('#__jsports_games', 'g'))
        ->innerJoin($db->quoteName('#__jsports_divisions', 'd') . ' ON ' . $db->quoteName('g.divisionid') . ' = ' . $db->quoteName('d.id'))
        ->where($db->quoteName('g.programid') . ' = :programid')
        ->where($db->quoteName('g.gamestatus') . ' = "C"')
        ->bind(':programid', $this->programid, ParameterType::INTEGER)
        ->group($db->quoteName('g.divisionid'))
        ->order($db->quoteName('d.ordering') . ' ASC');
        
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

