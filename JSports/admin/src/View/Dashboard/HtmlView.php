<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Table\LeaguesTable;

class HtmlView extends BaseHtmlView
{
    public $form;
    
    public function display($tpl = null)
    {
        $this->title = $this->get('Title');
        $this->version = $this->get('Version');
        
        //$db = Factory::getDbo();
         
        
//         $this->addToolBar();
        
        parent::display($tpl);
    }
    
    protected function addToolBar()
    {
//         ToolbarHelper::title(Text::_('JSports'));
//         ToolbarHelper::title(Text::_('Add'));
        
//         ToolbarHelper::apply('planet.apply');
    }
}