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

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Services\SponsorService;
use FP4P\Component\JSports\Site\Services\LogService;

/**
 * Controller for a BULLETIN
 *
 */
class SponsorController extends FormController
{
    

    public function deleteLogo() {
        
        $jinput = Factory::getApplication()->input;
        $files  = $jinput->files->get('jform', [], 'array');
        $sponsorid = $jinput->getInt('id');
        
        $filepath = SponsorHelper::getLogoFolder($sponsorid);
 
        if (SponsorService::deleteLogo($sponsorid)) {
            Factory::getApplication()->enqueueMessage("Sponsor Logo has been deleted", 'message');
            //             LogService::info("Attachment folder for Bulletin ID " . $bulletinid . " has been deleted");
            $this->setRedirect('index.php?option=com_jsports&view=sponsor&layout=edit&id=' . $sponsorid);
        } else {
            Factory::getApplication()->enqueueMessage("Attempt to delete Sponsor Logo has failed", 'message');
            $this->setRedirect('index.php?option=com_jsports&view=sponsor&layout=edit&id=' . $sponsorid);
        }
        
    }
    
    
}