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
namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use Joomla\CMS\Language\Text;

/**
 * Methods supporting a list of LEAGUE records.
 *
 * @since  1.0.0
 */
class PostscoresModel extends ListModel
{
    protected $team;
    protected $teamlastyearplayed;
    protected $programid;
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
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
// 		$id .= ':' . $this->team->id;
// 		$id .= ':' . $this->program->id;

		return parent::getStoreId($id);
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
 	       ->where($db->quoteName('a.programid') . ' = ' . $db->quote($this->programid))
 	       ->where($db->quoteName('a.gamestatus') . ' = ' . $db->quote('S'))
           	->where("(" . $db->quoteName('a.opponentid') . ' = ' . $db->quote($teamid) . ' or '
                . $db->quoteName('a.teamid') . ' = ' . $db->quote($teamid) .")")
           ->order("gamedate asc");
	    
//            $query->select("a.*, concat(a.awayteamname,\" @ \",a.hometeamname) as name");
//            $query->from($db->quoteName('#__jsports_games') . ' AS a')
//            ->where($db->quoteName('a.programid') . ' = ' . $db->quote($this->programid))
//            ->where($db->quoteName('a.awayteamid') . ' = ' . $db->quote($teamid))
//            ->orWhere($db->quoteName('a.hometeamid') . ' = ' . $db->quote($teamid))
//            ->order("gamedate asc");
           
	    return $query;
	}
	

	/**
	 * Method to get a list of GAME RECORDS.
	 * Overridden to add a check for access levels.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItems()
	{
		return parent::getItems();

	
	}
	
	
	
	
	
	/**
	 * This function is an override function of the FormModel validate function.
	 *
	 * @param unknown $form
	 * @param unknown $data
	 * @param unknown $group
	 * @return boolean|unknown
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