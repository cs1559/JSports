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

namespace FP4P\Component\JSports\Administrator\View\Registration;


defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

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
     * @var  \JForm
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
     * @var  \JObject
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
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');
        
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        $this->addToolbar();
        
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
        
        $toolbar = Toolbar::getInstance();
        
        ToolbarHelper::title(
            Text::_('COM_JSPORTS_REGISTRATION_PAGE_TITLE_' . ($isNew ? 'ADD' : 'EDIT'))
            );
        
        $canDo = ContentHelper::getActions('com_jsports');
        if ($canDo->get('core.create'))
        {
            $toolbar->apply('registration.apply');
            $toolbar->save('registration.save');
        }
        if ($isNew)
        {
            $toolbar->cancel('registration.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            $toolbar->cancel('registration.cancel', 'JTOOLBAR_CLOSE');
        }
        
        ToolbarHelper::help('help.html', true);
        
    }
}