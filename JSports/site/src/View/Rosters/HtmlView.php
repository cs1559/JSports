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

namespace FP4P\Component\JSports\Site\View\Rosters;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\RosterService;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;

class HtmlView extends BaseHtmlView
{
    public $form;
    
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;
    
    protected $team;
    protected $teamlastyearplayed;
    protected $program;
    protected $canEdit = false;
    protected $canAddPlayers = true;
    
    /**
     * The pagination object
     *
     * @var  \JPagination
     */
    protected $pagination;
    
    /**
     * The model state
     *
     * @var  \JObject
     */
    protected $state;
    
    /**
     * Form object for search filters
     *
     * @var  \JForm
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
        $this->team = $this->get('team') ;
        
        // NOTE:  Need to research to see if there is a better way of getting the model data into the template
        $mod = $this->getModel();
        $this->team = $mod->team;
        $this->teamlastyearplayed = $mod->teamlastyearplayed;
        $this->program = $mod->program;

        $this->canEdit = SecurityService::canEditTeamRoster($this->team->id,$this->program->id);
        $this->canAddPlayers = RosterService::canAddPlayers($this->team->id, $this->program->id);
        
        /*  2024-12-2  Changed logic in following line */
        if ($this->program->registrationonly) {
            $this->setLayout("unavailable");
        }
        if ($this->program->status == "C") {
            $this->setLayout("unavailable");
        }
               
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}

