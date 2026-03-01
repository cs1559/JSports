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



namespace FP4P\Component\JSports\Administrator\View\Sponsor;


defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Form\Form;

/**
 * View to edit a bulletin.
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
     * @var array<int, \stdClass>
     */
    protected $sponsorships = [];
    protected $assets = [];
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *;
     * @return  mixed  A string if successful, otherwise an Error object.
     *
     * @throws \Exception
     * @since   1.6
     */
    public function display($tpl = null)
    {
        
        /** @var \FP4P\Component\JSports\Administrator\Model\SponsorModel $model */
        $model = $this->getModel();
        
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');

        $this->sponsorships = $model->getSponsorships();
        $this->assets = $model->getAssets();
        
        // $this->hasAttachment = $this->item->hasAttachment;

        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        // Add PHONE formatter
        $document = Factory::getApplication()->getDocument();
        $wa = $this->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_jsports');
        $wa->useScript('com_jsports.phone-formatter.script');
        $phoneSelector = '#jform_contactphone';
        $document->addScriptOptions('com_jsports.phone', [
            'selector' => $phoneSelector
        ]);

        
        $wa->useStyle('com_jsports.jsports.style');
        
        
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
            Text::_('COM_JSPORTS_SPONSOR_PAGE_TITLE_' . ($isNew ? 'ADD' : 'EDIT'))
            );
        
        $canDo = ContentHelper::getActions('com_jsports');
        if ($canDo->get('core.create'))
        {
            $toolbar->apply('sponsor.apply');
            $toolbar->save('sponsor.save');
        }
        if ($isNew)
        {
            $toolbar->cancel('sponsor.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            $toolbar->cancel('sponsor.cancel', 'JTOOLBAR_CLOSE');
        }
        
        // Example: carry sponsorid to the other controller's add task
        $return = base64_encode('index.php?option=com_jsports&view=sponsor&layout=edit&id=' . (int) $this->item->id);
        $url = Route::_(
            'index.php?option=com_jsports&task=sponsorship.add'
            . '&sponsorid=' . $this->item->id . '&id=0&return=' . $return,
            false
            );
        
        $toolbar->link(Text::_('COM_JSPORTS_ADD_SPONSORSHIP'), $url)
        ->icon('icon-plus'); // optional, but works in J4/5 toolbar API
        
        $url = Route::_(
            'index.php?option=com_jsports&task=sponsorasset.add'
            . '&sponsorid=' . $this->item->id . '&id=0&return=' . $return,
            false
            );
        $toolbar->link('COM_JSPORTS_ADD_SPONSORASSET', $url)->icon('icon-plus');
        
        ToolbarHelper::help('help.html', true);
        
    }
}