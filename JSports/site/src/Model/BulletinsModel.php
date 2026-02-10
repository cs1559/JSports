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

namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\UserService;

/**
 * Methods supporting a list of LEAGUE records.
 *
 * @since  1.0.0
 */
class BulletinsModel extends ListModel
{
    protected $team;
    protected $teamlastyearplayed;
    protected $programid;
    protected $program;
    protected $recentprogram;
    
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.0.0
	 * @see     \Joomla\CMS\MVC\Controller\BaseController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
			);
		}

		parent::__construct($config);
	}


// 	/**
// 	 * Method to get a store id based on model configuration state.
// 	 *
// 	 * This is necessary because the model is used by the component and
// 	 * different modules that might need different sets of data or different
// 	 * ordering requirements.
// 	 *
// 	 * @param   string  $id  A prefix for the store id.
// 	 *
// 	 * @return  string  A store id.
// 	 *
// 	 * @since   1.0.0
// 	 */
// 	protected function getStoreId($id = '')
// 	{

// 		return parent::getStoreId($id);
// 	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	
	protected function getListQuery()
	{

	    $input = Factory::getApplication()->input;
	    
	    // Get Team Id
	    $teamid     = $input->getInt("teamid");
	    
	    $user = UserService::getUser();
	 	    
	    if ($teamid) {
	        $this->getTeam($teamid);
	    }

	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    if ($teamid) {
            $query->select("a.*");
    	    $query->from($db->quoteName('#__jsports_bulletins') . ' AS a')
     	       ->where($db->quoteName('a.teamid') . ' = ' . $db->quote($teamid))
               ->order("createdate desc");
	    } else {
	        $query->select("a.*");
	        $query->from($db->quoteName('#__jsports_bulletins') . ' AS a')
	        ->where($db->quoteName('a.ownerid') . ' = ' . $db->quote($user->id))
	        ->order("createdate desc");
	    }
	    
	    return $query;
	}
	
	private function getTeam($teamid) {

	    $svc = new TeamService();
	    $this->team = $svc->getItem($teamid);
	    $pgmArray = $svc->getMostRecentProgram($teamid);
	    $lastprogramid = $pgmArray['lastprogramid'];
	    $this->teamlastyearplayed = $lastprogramid;
	    $psvc = new ProgramsService();
	    $this->program = $psvc->getItem($lastprogramid);
	    
	}
		
}