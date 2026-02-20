<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Administrator\View\Reports;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
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
        $this->state            = $this->get('State');
        $this->filterForm       = $this->get('FilterForm');
//         $this->programs         = ProgramsService::getNonCompletedPrograms();
        $this->programs         = ProgramsService::getPrograms();

        $this->addToolbar();
        
        parent::display($tpl);
    }
    
    protected function addToolbar()
    {
        
        $toolbar = Toolbar::getInstance('toolbar');
        
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
