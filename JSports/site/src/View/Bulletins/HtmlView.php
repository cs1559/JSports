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

namespace FP4P\Component\JSports\Site\View\Bulletins;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Site\Services\SecurityService;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Pagination\Pagination;


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
    protected $program;
    protected $canEdit = false;
      
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
                       
        
        $context = array('teamid' => $this->team->id,
            'ownerid' => $this->team->ownerid,
            'programid' => $this->program->id
        );
        $this->canEdit = SecurityService::canEditTeam($context);
            
        //$this->canEdit = SecurityService::canEditTeamBulletins($this->team->id);

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}

