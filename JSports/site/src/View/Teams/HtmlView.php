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

namespace FP4P\Component\JSports\Site\View\Teams;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\SecurityService;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\Form;
use FP4P\Component\JSports\Site\Model\TeamsModel;
/**
 * This is the TEAMS view that allows a user to list teams.
 * @author Chris Strieter
 *
 */
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
    
    public $isProgramPending;
    
    public function display($tpl = null)
    {
        
        /** @var TeamsModel $model */  
        $model = $this->getModel();
        
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        
        $this->isProgramPending = false;
        
        /* removed 12/6/2024 - this caused pagination issues */
        //$this->pagination->limit = 30;
        
        $programid = (int) ($this->state->get('filter.programid') ?? 0);
        $this->program = ProgramsService::getItem($programid);
        
        $isAdmin = SecurityService::isAdmin();
        $status = $this->program->status ?? '';
        $setupFinal = !empty($this->program->setupfinal);
        
        $this->isProgramPending = ($status === 'P') || (!$setupFinal && (int) ($this->program->id ?? 0) > 0);
        
        if (!$this->program) {
            $this->setLayout('noprogram');
        }
        // Check for errors.       
        if (count($errors = $model->getErrors()))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
        
    }
    
}

