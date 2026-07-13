<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\View\Register;



defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\RegistrationService;

/**
 * HTML Registration View.  This is the initial view does nothing more than display a drop down for the
 * client to select a program they are selecting from.
 *
 * NOTE:  Not sure if canDo is really used.  Non-authenticated users can register a team.
 *
 * @
 */
class HtmlView extends BaseHtmlView
{

    protected $item;
        
    public function display($tpl = null)
    {
        $user       = $this->getCurrentUser();
        
        /** @var \FP4P\Component\JSports\Site\Model\RegisterModel $model */
        $model = $this->getModel();
        
        $this->data       = $model->getItem();
        $this->form       = $model->getForm($this->data,true);
                       
        // Check to see if the user can even register.
        $svc = new RegistrationService();
        $bool = $svc->isRegistrationAvailable();
        
        // If registration is unavailable, then stop processing and display any error messages.
        // NOTE:  Messages are set in the registrationservice.  It may be best to have them set someplace else.
        if (!$bool){
            return false;
        }
        
        $document = Factory::getApplication()->getDocument();
        $wa = $this->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_jsports');
        $wa->useScript('com_jsports.registration.script');
        
        return parent::display($tpl);
    }
}

