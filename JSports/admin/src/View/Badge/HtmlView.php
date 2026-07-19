<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace FP4P\Component\JSports\Administrator\View\Badge;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Form\Form;

/**
 * View to edit an article.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The \JForm object
     *
     * @var  Form
     */
    protected $form;
    
    /**
     * The active item
     *
     * @var  object
     */
    protected $item;
    
    /**
     * The model state
     *
     * @var  object
     */
    protected $state;
    
    /**
     * The actions the user is authorised to perform
     *
     * @var  object
     */
    protected $canDo;
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     *
     * @throws \Exception
     * @since   1.6
     */
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
        
        $this->addToolbar();
        
        $wa = $this->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_jsports');
        //$wa->useStyle('com_jsports.campaigns.style');
        $wa->useScript('com_jsports.badges.script');
        
        return parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @throws \Exception
     * @since   1.6
     */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $isNew      = ($this->item->id == 0);
        
        //         $toolbar = Toolbar::getInstance();
        $toolbar = $this->getDocument()->getToolbar();
        
        ToolbarHelper::title(
            Text::_('COM_JSPORTS_BADGE_PAGE_TITLE_' . ($isNew ? 'ADD' : 'EDIT'))
            );
        
        $canDo = ContentHelper::getActions('com_jsports');
        if ($canDo->get('core.create'))
        {
            $toolbar->apply('badge.apply');
            $toolbar->save('badge.save');
        }
        if ($isNew)
        {
            $toolbar->cancel('badge.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            $toolbar->cancel('badge.cancel', 'JTOOLBAR_CLOSE');
        }
        
        ToolbarHelper::help('help.html', true);
    }
}