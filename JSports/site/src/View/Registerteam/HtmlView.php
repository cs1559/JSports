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

namespace FP4P\Component\JSports\Site\View\Registerteam;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\RegistrationService;

/**
 * HTML Registration View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{

    protected $item;
   
    public function display($tpl = null)
    {
        $user       = $this->getCurrentUser();
        
        $this->data       = $this->get('Data');
        $this->state      = $this->get('State');
        $this->item       = $this->get('Item');
        $this->reports    = $this->get('Reports');
        
        
        $this->form               = $this->getModel()->getForm($this->data,true);
        
        // Check authorizations
        //        $this->canDo = ContentHelper::getActions('com_content', 'article', $this->item->id);
        $this->canDo = ContentHelper::getActions('com_jsports','core.register');
        
        if (!$user->authorise('core.register', 'com_jsports')) {
            Factory::getApplication()->enqueueMessage("You must be logged in to register", 'error');
            return false;
        }
               
        // Check to see if the user can even register.
        $svc = new RegistrationService();
        $bool = $svc->isRegistrationAvailable();
        
        if (!$bool){
            return false;
        }
        
        
        // Check for errors.
        //         if (count($errors = $this->get('Errors')))
            //         {
            //             throw new GenericDataException(implode("\n", $errors), 500);
            //         }
        
        return parent::display($tpl);
    }
}

