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

namespace FP4P\Component\JSports\Site\View\Rosters;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\RosterService;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Pagination\Pagination;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Site\Model\RostersModel;
use FP4P\Component\JSports\Administrator\Table\ProgramsTable;
/**
 * This particular class is the HMTL view that will list all staff/players for a current program
 * @author Chris Strieter
 *
 */
class HtmlView extends BaseHtmlView
{
    /**
     * 
     * @var Form
     */
    public $form;
    /**
     * @var array
     */
    protected $items;
    
    /**
     * @var TeamsTable 
     */
    protected $team;
    protected $teamlastyearplayed;
    
    /**
     * @var ProgramsTable 
     */
    protected $program;
    /**
     * 
     * @var boolean
     */
    protected $canEdit = false;
    
    /**
     * 
     * @var boolean
     */
    protected $canAddPlayers = true;
    
    /**
     * 
     * @var Pagination
     */
    protected $pagination;
    
    /**
     * 
     * @var object
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
        /** @var \FP4P\Component\JSports\Site\Model\RostersModel $model */
        $model = $this->getModel();
        
        try {
            $this->items              = $model->getItems();
            $this->state              = $model->getState();
            $this->team               = $model->getTeam();
            $this->teamlastyearplayed = $model->teamlastyearplayed;
            $this->program            = $model->getProgram();
            
            $this->canEdit       = SecurityService::canEditTeamRoster($this->team->id, $this->program->id);
            $this->canAddPlayers = RosterService::canAddPlayers($this->team->id, $this->program->id);
            
            // Choose layout AFTER you have $program
            if (!empty($this->program->registrationonly) || ($this->program->status ?? '') === 'C') {
                $this->setLayout('unavailable');
            }
            
            return parent::display($tpl);
            
        } catch (\Throwable $e) {
            throw new GenericDataException($e->getMessage(), 500, $e);
        }
    }
       
}

