<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Administrator\View\Reports;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use FP4P\Component\JSports\Site\Services\ProgramsService;

class HtmlView extends BaseHtmlView
{
    protected $state;
    protected $filterForm;
    protected $programs;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->items         = $model->getItems();
//         $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
//         $this->filterForm    = $model->getFilterForm();
//         $this->activeFilters = $model->getActiveFilters();
        
        if (count($errors = $model->getErrors()))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
//         $this->programs         = ProgramsService::getNonCompletedPrograms();
//@TODO  This call to ProgramsService should be placed within the model.
        $this->programs         = ProgramsService::getPrograms();

        $this->addToolbar();
        
//         $document = Factory::getApplication()->getDocument();
        $wa = $this->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_jsports');
        $wa->useStyle('com_jsports.reports.style');
        
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        
        //         $toolbar = Toolbar::getInstance();
        $toolbar = $this->getDocument()->getToolbar();
        
        ToolbarHelper::title(Text::_('Sports Managment - Reports'));
        
        $toolbar->standardButton('dashboard')
        ->icon('fa fa-home')
        ->text('Dashboard')
        ->task('display.dashboard')   // probably need to make this dashboard.display
        ->listCheck(false);
       
        $toolbar->preferences('com_jsports');
        ToolbarHelper::help('help.html', true);
    }
}
