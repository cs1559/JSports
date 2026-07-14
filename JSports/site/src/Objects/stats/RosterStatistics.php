<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */
namespace FP4P\Component\JSports\Site\Objects\stats;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

class RosterStatistics
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

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
             
        $query->select('count(case when classification = "P" then 1 end) totalplayers,
            count(case when classification = "S" then 1 end) totalstaff'
            );
        $query->from($db->quoteName('#__jsports_rosters') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.programid') . ' = ' . $db->quote($this->programid)
        );
        $query->where($conditions);

        $db->setQuery($query);
        $rows = $db->loadAssoc();        
        return $rows;
        
    }
    
}

