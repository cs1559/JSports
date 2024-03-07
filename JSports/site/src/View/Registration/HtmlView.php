<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\View\Registration;



defined('_JEXEC') or die;

// use FP4P\Component\JSports\Site\Services\Registration;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\CMSObject;

/**
 * HTML Registration View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The item model state
     *
     * @var    \Joomla\Registry\Registry
     * @since  1.6xxx
    /**
     * The item object details
     *
     * @var    \JObject
     * @since  1.6
     */
    protected $item;
    
    /**
     * The list of visit reports/visit dates for this walk
     *
     * @var    \JObject
     * @since  1.6
     */
    protected $agreementurl = "";
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $this->data       = $this->get('Data');
        $this->state      = $this->get('State');
        $this->item       = $this->get('Item');

        $isNew = false;
        $programid = Factory::getApplication()->getUserState('com_jsports.edit.registration.programid',0);
        if ($programid) {
            $isNew = true;
        }
        
        $this->form        = $this->getModel()->getForm($this->item,true);
        
        if ($isNew) {
            $this->item->programid = $programid;
        }
        
        
        $this->agreementurl = $app->getUserState('com_jsports.edit.registration.agreementurl', '');
     
        // Cleanout the agreement url state
        $app->setUserState('com_jsports.edit.registration.agreementurl','');
        
        
        $this->form->bind($this->item);      
        
         // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
    }
}