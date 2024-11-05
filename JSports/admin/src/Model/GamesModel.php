<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
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

	    $input = Factory::getApplication()->input;
	    $input = $input->post->get('jform', array(), 'array');

	    if (array_key_exists('gameid', $input)) {
	        $_gameid = $input['gameid'];
	        $this->setState('gameid', $gameid);
// 	        $this->setState('gameid', $_gameid);
	    }
	    
	    
	    if (array_key_exists('programid', $input)) {
    	    $_programid = $input['programid'];
	        $this->setState('programid', $programid);
// 	        $this->setState('programid', $_programid);
	    }
	    
	    if (array_key_exists('divisionid', $input)) {
    	    $_divisionid = $input['divisionid'];
            $this->setState('divisionid', $divisionid);
	    }
	    
	    if (array_key_exists('teamid', $input)) {
	       $_teamid = $input['teamid'];
	       $this->setState('teamid', $teamid);
	    }
	    
		// List state information.
		parent::populateState($ordering, $direction);
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
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
//		$id .= ':' . $this->getState('filter.published');
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
	    
	    $input = Factory::getApplication()->input;
	    $input = $input->post->get('jform', array(), 'array');

// 	    if (array_key_exists('gameid', $input)) {
// 	        $_gameid = $input['gameid'];
// 	    } else {
// 	        $_gameid = 0;
// 	    }
	    
//  	    if (array_key_exists('programid', $input)) {
// 	       $_programid = $input['programid'];
//  	    } else {
//  	        $_programid = 0;
//  	    }
 	    
// 	    if (array_key_exists('divisionid', $input)) {
// 	       $_divisionid = $input['divisionid'];
// 	    } else {
// 	        $_divisionid = null;
// 	    }
	    
// 	    if (array_key_exists('teamid', $input)) {
// 	       $_teamid = $input['teamid'];
// 	    } else {
// 	        $_teamid = null;
// 	    }

	    $_programid = (string) $this->getState('filter.programid');
	    $_divisionid = (string) $this->getState('filter.divisionid');
	    $_teamid = (string) $this->getState('filter.teamid');
	    $_gameid = (string) $this->getState('filter.gameid');
	    
    
	    // Create a new query object.
	    $db    = $this->getDatabase();
	    $query = $db->getQuery(true);
	    
	    // Select the required fields from the table.
	    $query->select(
	        $this->getState(
	            'list.select',
	            'a.*'
	            )
	        );
	    $query->from($db->quoteName('#__jsports_games') . ' AS a');   

	    // Filter by GAME ID
	    $gameid = $_gameid;
	    if (is_numeric($gameid))
	    {
	        $query->where($db->quoteName('a.id') . ' = :gameid');
	        $query->bind(':gameid', $gameid, ParameterType::INTEGER);
	    }
	    
	    // Filter by DIVISION ID
	    $divisionid = $_divisionid;
	    if (is_numeric($divisionid))
	    {
	        $query->where($db->quoteName('a.divisionid') . ' = :divisionid');
	        $query->bind(':divisionid', $divisionid, ParameterType::INTEGER);
	    }
	    
	    $programid = $_programid;
	    if (is_numeric($programid))
	    {
	        $query->where($db->quoteName('a.programid') . ' = :programid');
	        $query->bind(':programid', $programid, ParameterType::INTEGER);
	    }
	    
	    $teamid = $_teamid;
	    if (is_numeric($teamid))
	    {
	        $query->where('(' . $db->quoteName('a.hometeamid') . ' = :hteamid or ' . $db->quoteName('a.awayteamid') . ' = :ateamid )');
	        $query->bind(':hteamid', $teamid, ParameterType::INTEGER);
	        $query->bind(':ateamid', $teamid, ParameterType::INTEGER);
	    }
	   
	    // Add the list ordering clause.
// 	    $orderCol  = $this->state->get('list.ordering', 'a.id');
// 	    $orderDirn = $this->state->get('list.direction', 'ASC');
	    
        $query->order($db->escape('gamedate asc'));
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
	        
	        /* this is a very simple method to change the state of each item selected */
	        $db = $this->getDatabase();
	        
	        $query = $db->getQuery(true);
	        
	        /* 2/12/2024 - added $db->quote to $value */
	        $query->update($db->quoteName('#__jsports_programs'))
	        ->set($db->quoteName('published') . ' = :value')
	        ->bind(':value', $db->quote($value) , ParameterType::INTEGER)
	        ->whereIn($db->quoteName('id'), $pks);
	        $db->setQuery($query);
	        $db->execute();
	    }
	    
	
}