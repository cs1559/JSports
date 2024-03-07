<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     0.0.1
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller for a single REGISTRATION
 *
 * @since  1.6
 */
class RegistrationController extends FormController
{
    
    public function save($key = null, $urlVar = null) {
        
//         $data = $this->input->post->get('jform', array(), 'array');
        
//         $data['name'] = $data['name'];
        
        
        parent::save($key, $urlVar);
        
    }
    
    
}