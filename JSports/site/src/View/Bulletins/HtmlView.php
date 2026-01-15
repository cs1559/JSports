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

namespace FP4P\Component\JSports\Site\View\Bulletins;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Site\Services\SecurityService;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Pagination\Pagination;

/**
 * HTML Bulletins View
 *
 * - 01/15/2026 - Refactored to change $this->get to $model->getxxx to commply with future Joomla releases.
 *
 * @since  1.5
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
    
    /** @var TeamsTable */
    protected $team;
    protected $program;
    protected $canEdit = false;
    
    
    public function display($tpl = null)
    {

        /** @var \FP4P\Component\JSports\Site\Model\BulletinsModel $model */
        $model = $this->getModel('Bulletins');
        
        $this->items                = $model->getItems();
        $this->pagination           = $model->getPagination();
        $this->state                = $model->getState();
        $this->filterForm           = $model->getFilterForm();
        $this->activeFilters        = $model->getActiveFilters();
        $this->team                 = $model->team;
        $this->teamlastyearplayed   = $model->teamlastyearplayed;
        $this->program              = $model->program;
                       
        $context = array('teamid' => $this->team->id,
            'ownerid' => $this->team->ownerid,
            'programid' => $this->program->id
        );
        $this->canEdit = (bool) SecurityService::canEditTeam($context);

        // Check for errors.
        $errors = $model->getErrors();
        if (!empty($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}

