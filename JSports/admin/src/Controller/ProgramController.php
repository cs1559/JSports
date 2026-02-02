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

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\LogService;
use Joomla\CMS\Router\Route;

/**
 * Controller for a single Program
 *
 * @since  1.6
 */
class ProgramController extends FormController
{
    
    public function save($key = null, $urlVar = null) {
        
        $data = $this->input->post->get('jform', array(), 'array');
        LogService::writeArray($data, 'PROGRAM');
        return parent::save($key, $urlVar);
        
    }
    
    public function setup() {
        
        $this->checkToken();
        
//         // Optional but recommended: ACL
//         if (!$this->app->getIdentity()->authorise('core.edit', 'com_jsports'))
//         {
//             throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
//         }
        
        $programid = $this->input->getInt('id', 0);
        
        $this->setRedirect(
            Route::_('index.php?option=com_jsports&view=programsetup&programid=' . $programid, false)
            );
        
        return true;
    }
    
}