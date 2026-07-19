<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Administrator\View\JSports;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;

class HtmlView extends BaseHtmlView
{
    public $form;
    
    public function display($tpl = null)
    {
        $model = $this->getModel();
        $this->form  = $model->getForm();
        $this->item  = $model->getItem();
        $this->state = $model->getState();
        
        if (count($errors = $model->getErrors()))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        $this->addToolBar();
        
        parent::display($tpl);
    }
    
    protected function addToolBar()
    {
        ToolbarHelper::title(Text::_('JSports'));
        ToolbarHelper::title(Text::_('Add'));
        
//        ToolbarHelper::apply('planet.apply');
    }
}