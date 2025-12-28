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

namespace FP4P\Component\JSports\Site\View\Schedules;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\DivisionService;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\Form;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;


class HtmlView extends BaseHtmlView
{
    public $form;
    
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;

    /**
     * 
     * @var TeamsTable
     */
    protected $team;
    
    protected $teamlastyearplayed;
    protected $program;
    
    /**
     * 
     * @var boolean
     */
    protected $canEdit = false;
      
    /**
     * The pagination object
     *
     * @var  Pagination
     */
    protected $pagination;
    
    /**
     * The model state
     *
     * @var  object
     */
    protected $state;
    
    /**
     * Form object for search filters
     *
     * @var  Form
     */
    public $filterForm;
    
    /**
     * The active search filters
     *
     * @var  array
     */
    public $activeFilters;
    
      
    public function display($tpl = null)
    {
        
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        // NOTE:  Need to research to see if there is a better way of getting the model data into the template
        $mod = $this->getModel();
        $this->team = $mod->team;
        $this->teamlastyearplayed = $mod->teamlastyearplayed;
        $this->program = $mod->program;
                       
        $this->canEdit = SecurityService::canEditTeamSchedule($this->team->id,$this->program->id);

        $divisionid = TeamService::getTeamDivisionId($this->team->id, $this->program->id);
        $division = DivisionService::getItem($divisionid);
        
        /* Updated 12-2-2024 */
        if ($this->program->registrationonly) {
            $this->setLayout("unavailable");
        }
        if ($this->program->status == 'C') {
            $this->setLayout("unavailable");
        }
        if ($this->program->status == 'P') {
            $this->setLayout("unavailable");
        }
        if (!$this->program->setupfinal && !SecurityService::isAdmin())  {
            $this->setLayout("unavailable");
        }
        if ($division->leaguemanaged && !SecurityService::isAdmin())  {
            $this->setLayout("leaguemanaged");
        }
        
        
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}

