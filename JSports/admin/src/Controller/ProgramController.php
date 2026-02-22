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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\LogService;
use Joomla\CMS\Router\Route;
use Joomla\Session\Session;

/**
 * Controller for a single Program
 *
 * @since  1.6
 */
class ProgramController extends FormController
{

    /**
     * The SAVE function overrides the base function in order to log the data when a program record is updated.
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Controller\FormController::save()
     */
    public function save($key = null, $urlVar = null) {
 
        $result = parent::save($key, $urlVar);
        
        if ($result) {
            $data = $this->input->post->get('jform', array(), 'array');
            LogService::writeArray($data, 'PROGRAM');
        }
        
        return $result;
        
    }
    
    /**
     * The setup function essentially is used to redirect the client to the program setup screen.
     */
    public function setup() : void {
        
        $this->checkToken();
        
//         // Optional but recommended: ACL
//         if (!$this->app->getIdentity()->authorise('core.edit', 'com_jsports'))
//         {
//             throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
//         }
        
//         $programid = $this->input->getInt('id', 0);

        $data = $this->input->post->get('jform', [], 'array');
        $programid = (int) ($this->input->getInt('id') ?: ($data['id'] ?? 0));
        
        $this->setRedirect(
            Route::_('index.php?option=com_jsports&view=programsetup&programid=' . $programid . 
                '&' . Session::getFormToken() . '=1', false)
            );
        
        return true;
    }
    
}