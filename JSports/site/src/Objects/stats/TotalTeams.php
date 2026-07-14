<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Objects\stats;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

class TotalTeams
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
     
        $query->select('p.*');
        $query->from($db->quoteName('#__jsports_map') . ' AS p ');
        $conditions = array(
            $db->quoteName('p.published') . ' in (1) ',
            $db->quoteName('p.programid') . ' = ' . $db->quote($this->programid)
        );
        $query->where($conditions);
//         $query->order("id desc");
        $db->setQuery($query);

        $rows = $db->loadAssocList();
        return count($rows);
        
    }
    
}

