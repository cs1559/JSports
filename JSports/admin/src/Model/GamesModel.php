<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

/**
 * Methods supporting a list of mywalks records.
 *
 */
class GamesModel extends ListModel
{
    
    
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     \Joomla\CMS\MVC\Controller\BaseController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name', 'a.name',
			    'gameid', 'a.id',
			    'programid', 'a.programid',
			    'divisionid', 'a.divisionid',
			    'teamid', 'a.teamid',
			);
		}

		parent::__construct($config);
	}


	public function getForm($data = array(), $loadData = true)
	{
	    // Get the form.
	    $form = $this->loadForm('com_jsports.games', 'games', array('control' => 'jform', 'load_data' => $loadData));
	    
	    if (empty($form))
	    {
	        return false;
	    }
	    
	    return $form;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.name', $direction = 'asc')
	{
	    
	    $app = Factory::getApplication();
	    
	    // Typical admin list state
	    $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
	    $this->setState('filter.search', $search);
	    
	    $gameid     = $this->getUserStateFromRequest($this->context . '.filter.gameid', 'filter_gameid', '', 'int');
	    $programid  = $this->getUserStateFromRequest($this->context . '.filter.programid', 'filter_programid', '', 'int');
	    $divisionid = $this->getUserStateFromRequest($this->context . '.filter.divisionid', 'filter_divisionid', '', 'int');
	    $teamid     = $this->getUserStateFromRequest($this->context . '.filter.teamid', 'filter_teamid', '', 'int');
	    $viewname     = $this->getUserStateFromRequest($this->context . '.filter.viewname', 'filter_viewname', '', 'string');
	    
	    $this->setState('filter.gameid', $gameid);
	    $this->setState('filter.programid', $programid);
	    $this->setState('filter.divisionid', $divisionid);
	    $this->setState('filter.teamid', $teamid);
	    $this->setState('filter.viewname', $viewname);
	    
	    
	    parent::populateState($ordering, $direction);
	    
	    // If you truly want to override pagination:
	    $this->setState('list.start', 0);
	    $this->setState('list.limit', 500);
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
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
	    $id .= ':' . $this->getState('filter.search');
	    $id .= ':' . $this->getState('filter.gameid');
	    $id .= ':' . $this->getState('filter.programid');
	    $id .= ':' . $this->getState('filter.divisionid');
	    $id .= ':' . $this->getState('filter.teamid');
	    
	    return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery
	 *
	 * @since   1.6
	 */
	
	protected function getListQuery()
	{
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    $query->select($this->getState('list.select', 'a.*'))
	    ->from($db->quoteName('#__jsports_games', 'a'));
	    
	    $gameid     = (int) $this->getState('filter.gameid');
	    $programid  = (int) $this->getState('filter.programid');
	    $divisionid = (int) $this->getState('filter.divisionid');
	    $teamid     = (int) $this->getState('filter.teamid');
	    
	    // Filter by GAME ID
	    if ($gameid > 0) {
	        $query->where($db->quoteName('a.id') . ' = :gameid')
	        ->bind(':gameid', $gameid, ParameterType::INTEGER);
	    }
	    
	    // Filter by PROGRAM ID
	    if ($programid > 0) {
	        $query->where($db->quoteName('a.programid') . ' = :programid')
	        ->bind(':programid', $programid, ParameterType::INTEGER);
	    }
	    
	    // Filter by DIVISION ID
	    if ($divisionid > 0) {
	        $query->where($db->quoteName('a.divisionid') . ' = :divisionid')
	        ->bind(':divisionid', $divisionid, ParameterType::INTEGER);
	    }
	    
	    // Filter by TEAM ID (either home or away)
	    if ($teamid > 0) {
	        $query->where(
	            '(' . $db->quoteName('a.hometeamid') . ' = :teamid OR ' .
	            $db->quoteName('a.awayteamid') . ' = :teamid)'
	            )->bind(':teamid', $teamid, ParameterType::INTEGER);
	    }
	    
	    // Sorting (simple)
	    $query->order($db->quoteName('a.gamedate') . ' ASC');
	    
	    return $query;
	}
	

	/**
	 * Method to get a list of proggrams.
	 * Overridden to add a check for access levels.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   4.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}


	    /**
	     * Method to change the published state of one or more records.
	     *
	     * @param   array    &$pks   A list of the primary keys to change.
	     * @param   integer  $value  The value of the published state.
	     *
	     * @return  boolean  True on success.
	     *
	     * @since   4.0.0
	     */
	    public function publish(&$pks, $value = 1) {
	        
	        $db = $this->getDatabase();
	        $pks = array_map('intval', (array) $pks);
	        
	        if (!$pks) {
	            return false;
	        }
	        
	        $query = $db->getQuery(true)
	        ->update($db->quoteName('#__jsports_games'))
	        ->set($db->quoteName('published') . ' = :value')
	        ->whereIn($db->quoteName('id'), $pks)
	        ->bind(':value', (int) $value, ParameterType::INTEGER);
	        
	        $db->setQuery($query);
	        $db->execute();
	        
	        return true;
	    }
	    
	
}