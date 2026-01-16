<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
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
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Form;
use FP4P\Component\JSports\Administrator\Table\ProgramsTable;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;

/**
 * Post Scores Model - generates a list of games that are eligible for posting of a score.
 *
 * @since  1.0.0
 */
class PostscoresModel extends ListModel
{
    /**
     * Team record
     * @var TeamsTable
     */
    protected $team;
    
    /**
     * Last Year Played
     * @deprecated
     * @var int
     */
    protected $teamlastyearplayed;
    
    /**
     * Program Id
     * @var int
     */
    protected $programid;
    
    /** 
     * Program record
     * @var ProgramsTable
     */
    protected $program;
    
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
	    $programid     = $input->getInt("programid");
	    
	    $svc = new TeamService();
	    $this->team = $svc->getItem($teamid);
	    $result = $svc->getMostRecentProgram($teamid);

	    if (is_null($programid) || $programid == 0) {
    	    if (isset($result['lastprogramid'])) {
    	        $this->programid = $result['lastprogramid'];
    	    } else {
    	        $this->programid = null;
    	    }
	    } else {
	        $this->programid = $programid;
	    }
		    
	    $psvc = new ProgramsService();
	    $this->program = $psvc->getItem($this->programid);
	    
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
        $query->select("a.*");
	    $query->from($db->quoteName('#__jsports_games') . ' AS a')
 	       ->where($db->quoteName('a.programid') . ' = :programid ' )
 	       ->where($db->quoteName('a.gamestatus') . ' = ' . $db->quote('S'))
           	->where("(" . $db->quoteName('a.opponentid') . ' = :opponentid or  ' 
                . $db->quoteName('a.teamid') . ' = :teamid)' )
           ->order("gamedate asc");
	    
        $query->bind(':programid', $this->programid, ParameterType::INTEGER);
        $query->bind(':opponentid', $teamid, ParameterType::INTEGER);
        $query->bind(':teamid', $teamid, ParameterType::INTEGER);
           
	    return $query;
	}
	

	public function getTeam() : TeamsTable {
	    return $this->team;
	}
	
	public function getProgram() : ProgramsTable {
	    return $this->program;
	}
	
// 	/**
// 	 * Method to get a list of GAME RECORDS.
// 	 * Overridden to add a check for access levels.
// 	 *
// 	 * @return  mixed  An array of data items on success, false on failure.
// 	 *
// 	 * @since   1.0.0
// 	 */
// 	public function getItems()
// 	{
// 		return parent::getItems();

	
// 	}
		
	/**
	 * This function is an override function of the FormModel validate function.
	 *
	 * @param Form $form
	 * @param array $data
	 * @param string $group
	 * @return array|boolean
	 */
	public function validate($form, $data, $group = null) {
	    
	    if (!key_exists("teamid", $data)) {
	        $this->setError(Text::_('COM_JSPORTS_MISSING_TEAMID'));
	        return false;
	    }
	    if (!is_numeric($data['hometeamscore'])) {
	        $this->setError(Text::_('COM_JSPORTS_INVALID_HOMETEAMSCORE'));
	        return false;
	    }
	    if (!is_numeric($data['awayteamscore'])) {
	        $this->setError(Text::_('COM_JSPORTS_INVALID_AWAYTEAMSCORE'));
	        return false;
	    }
        return $data;
	}
}