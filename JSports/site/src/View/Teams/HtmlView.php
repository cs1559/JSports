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

namespace FP4P\Component\JSports\Site\View\Teams;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\SecurityService;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\Form;

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
    
    public $showData;
    public $isProgramPending;
    
    public function display($tpl = null)
    {
        
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->isProgramPending = false;
        
        /* removed 12/6/2024 - this caused pagination issues */
        //$this->pagination->limit = 30;
        
        $programid = $this->state['filter.programid'];
        $this->program = ProgramsService::getItem($programid);
        
        $this->showData = false;
        if ($this->program->status == "P") {
            $this->isProgramPending = true;
            if (SecurityService::isAdmin()) {
                $this->showData = true;
            }
            $this->showData = false;
        } else {
            $this->showData = true;
        }
        
        if (!$this->program->setupfinal && $this->program->id > 0) {
            $this->isProgramPending = true;
        }
         
        // Check for errors.       
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
        
    }
    
   
}

