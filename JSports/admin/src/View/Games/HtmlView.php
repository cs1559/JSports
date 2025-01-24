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
namespace FP4P\Component\JSports\Administrator\View\Games;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Table\LeaguesTable;

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
        $this->form          = $this->get('Form');
        $this->activeFilters = $this->get('ActiveFilters');
    
       
        // Obtain the state variables and bind them to the search form
        $_programid = $this->state->get('programid');
        $_divisionid = $this->state->get('divisionid');
        $_teamid = $this->state->get('teamid');
        $_gameid = $this->state->get('gameid');
        $defaults = array('programid' => $_programid, 
                'divisionid' => $_divisionid,
                'teamid' => $_teamid,
                'gameid' => $_gameid,
        );
        $this->form->bind($defaults);
        
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        
        $this->addToolbar();
        
        return parent::display($tpl);
        
        
    }
    
    protected function addToolBar()
    {
        
        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('Sports Managment - Games/Schedules'));
               
        $canDo = ContentHelper::getActions('com_jsports');       
        
        if ($canDo->get('core.create'))
        {
            $toolbar->addNew('game.add');
        }
        
        if ($canDo->get('core.edit.state'))
        {
            $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('icon-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);
            
            $childBar = $dropdown->getChildToolbar();
            
//             $childBar->publish('games.publish')->listCheck(true);
            
//             $childBar->unpublish('games.unpublish')->listCheck(true);
            
//             $childBar->archive('games.archive')->listCheck(true);
            
//             if ($this->state->get('filter.published') != -2)
//             {
                $childBar->trash('games.trash')->listCheck(true);
//             }
        }
        
        if ($canDo->get('core.delete'))
        {
            $toolbar->delete('games.delete')
            ->text('JTOOLBAR_EMPTY_TRASH')
            ->message('JGLOBAL_CONFIRM_DELETE')
            ->listCheck(true);
        }
        
        $toolbar->standardButton('dashboard')
        ->icon('fa fa-home')
        ->text('Dashboard')
        ->task('display.dashboard')
        ->listCheck(false);
        
        
        if ($canDo->get('core.create'))
        {
            $toolbar->preferences('com_jsports');
        }

        ToolbarHelper::help('help.html', true);
    }
   
}