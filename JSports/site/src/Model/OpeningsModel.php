<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;

/**
 * OpeningsModel
 * 
 * 01/15/2026 - Refactored code and eliminated unnecessary code
 *
 */
class OpeningsModel extends ListModel
{
    
    protected $programid;
    

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.6
	 */
	
	protected function getListQuery()
	{
	    
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    $query->select(
	        $this->getState(
	            'list.select',
	            'a.*, b.programid, c.agegroup, c.name as divisionname, concat(a.name,a.contactname) as searchfield'
	            )
	        );
        $query->from($db->quoteName('#__jsports_teams') . ' AS a, ' .
            $db->quoteName('#__jsports_map') . ' AS b,  '  .
            $db->quoteName('#__jsports_divisions') . ' AS c ' );
        $query->where($db->quoteName('b.programid') . ' in (select id from #__jsports_programs where status <> "C" and published = 1) ');
        $query->where($db->quoteName('a.openroster') . ' = 1' );
        $query->where($db->quoteName('a.id') . ' = ' . $db->quoteName('b.teamid'));
        $query->where($db->quoteName('b.divisionid') . ' = ' . $db->quoteName('c.id'));
	    
	    return $query;
	}
	    
	
}