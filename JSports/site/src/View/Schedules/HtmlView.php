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

namespace FP4P\Component\JSports\Site\View\Schedules;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\DivisionService;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Pagination\Pagination;
// use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\Form;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Site\Model\SchedulesModel;
use FP4P\Component\JSports\Administrator\Table\ProgramsTable;

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
    
    /**
     * 
     * @var ProgramsTable 
     */
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
       
      
    public function display($tpl = null)
    {
        /** @var SchedulesModel $model */
        $model = $this->getModel();
        
        try {
            $this->items                = $model->getItems();
            $this->state                = $model->getState();
            $this->pagination           = $model->getPagination();
    
            $this->team                 = $model->getTeam();
            $this->teamlastyearplayed   = $model->teamlastyearplayed;
            $this->program              = $model->getProgram();
                           
            $this->canEdit = SecurityService::canEditTeamSchedule($this->team->id,$this->program->id);
            $divisionid = TeamService::getTeamDivisionId($this->team->id, $this->program->id);
            $division = DivisionService::getItem($divisionid);
            
            /* Updated 12-2-2024 */
            // Consolidate unavailable rules
            $isUnavailable =
            !empty($this->program->registrationonly)
            || in_array(($this->program->status ?? ''), ['C', 'P'], true)
            || (empty($this->program->setupfinal) && !SecurityService::isAdmin());
            
            if ($isUnavailable) {
                $this->setLayout('unavailable');
            }
            
//             if ($this->program->registrationonly) {
//                 $this->setLayout("unavailable");
//             }
//             if ($this->program->status == 'C') {
//                 $this->setLayout("unavailable");
//             }
//             if ($this->program->status == 'P') {
//                 $this->setLayout("unavailable");
//             }
//             if (!$this->program->setupfinal && !SecurityService::isAdmin())  {
//                 $this->setLayout("unavailable");
//             }
//             if ($division->leaguemanaged && !SecurityService::isAdmin())  {
//                 $this->setLayout("leaguemanaged");
//             }
            
            // Check for errors.
            if (count($errors = $model->getErrors()))
            {
                throw new GenericDataException(implode("\n", $errors), 500);
            }
                    
            return parent::display($tpl);
        
        } catch (\Throwable $e) {
            throw new GenericDataException($e->getMessage(), 500, $e);
        }
    }
       
}

