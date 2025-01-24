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
namespace FP4P\Component\JSports\Administrator\View\Logs;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

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
        $this->activeFilters = $this->get('ActiveFilters');
        
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

        ToolbarHelper::title(Text::_('Sports Managment - View System/User Logs'));
               
        $canDo = ContentHelper::getActions('com_jsports');     

        $toolbar->delete('purge')
        ->icon('icon-cog')
        ->text('COM_JSPORTS_PURGELOGS')
        ->message('JGLOBAL_CONFIRM_DELETE')
        ->task('logs.purge')
        ->listCheck(false);

        $toolbar->standardButton('dashboard')
        ->icon('fa fa-home')
        ->text('Dashboard')
        ->task('display.dashboard')
        ->listCheck(false);
        
        if ($canDo->get('core.create'))
        {
            $toolbar->preferences('com_jsports');
        }
        
    }
   
}

