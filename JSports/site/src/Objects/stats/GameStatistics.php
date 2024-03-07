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
namespace FP4P\Component\JSports\Site\Objects\stats;

use Joomla\CMS\Factory;

class GameStatistics
{
    protected $programid = 0;
    
    public function __construct(array $context) {
        

        if (key_exists('programid', $context)) {
            $this->programid = $context['programid'];
        }

    }
    
    public function getData() {

        if ($this->programid == 0) {
            return 0;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
             
        $query->select('count(id) totalgames,
            count(case when gamestatus = "S" then 1 end) gamesscheduled,
            count(case when gamestatus = "C" then 1 end) gamescompleted,
            count(case when gamestatus not in ("S", "C") then 1 end) gamesother'
            );
        $query->from($db->quoteName('#__jsports_games') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.programid') . ' = ' . $db->quote($this->programid)
        );
        $query->where($conditions);

        $db->setQuery($query);
        $rows = $db->loadAssoc();        
        return $rows;
        
    }
    
}

