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
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Services\ProgramsService;

/**
 * Methods supporting a list of Teams.
 *
 */
class PostingsModel extends ListModel
{
    
    /**
     * @deprecated
     */
    protected $programid;
    
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
				'category', 'a.category',
			);
		}
		parent::__construct($config);
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
	        
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$category = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '');
		$this->setState('filter.category', $category);
				
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
		$id .= ':' . $this->getState('filter.category');
		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.  NOTE:  This function/query should not load anything PER DESIGN
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
	    
	    // Select the required fields from the table.
	    $query->select(
	        $this->getState(
	            'list.select',
	            'a.*'
	            )
	        );
	    
	    $category          = (string) $this->getState('filter.category');
	    $search                = (string) $this->getState('filter.search');
	   	
	   	$query->from($db->quoteName('#__jsports_bulletins') . ' AS a ');
	   	$query->where($db->quoteName('a.approved') . ' = 1');

	    // Filter by bulletin type`   
	    if (!empty($category))
	    {
	        $query->where($db->quoteName('a.category') . ' = :category');
	        $query->bind(':category', $category, ParameterType::INTEGER);
	    }

	    if (!empty($search))
	    {
	        $search = '%' . trim($search) . '%';
	        $query->where('UPPER(concat(a.title,a.content)) LIKE upper(:search)')
	           ->bind(':search', $search, ParameterType::STRING);
	    }
	        
// 	    // Add the list ordering clause.
// 	    $orderCol  = $this->state->get('list.ordering', 'a.name');
// 	    $orderDirn = $this->state->get('list.direction', 'asc');
	    
	    $query->order('a.createdate desc');
	    
	    return $query;
	    
	}	    

}