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
 * Controller for a single Team
 *
 */
class GameController extends FormController
{
    
    public function savenew($key = null, $urlVar = null) {
        
        //         $data = $this->input->post->get('jform', array(), 'array');
        
        //         $data['name'] = $data['name'];
        
        
        parent::save($key, $urlVar);
        
        //http://localhost:8081/administrator/index.php?option=com_jsports&view=game&layout=edit
        
        $this->redirect('index.php?option=com_jsports&view=game&layout=edit');
    }
    
}