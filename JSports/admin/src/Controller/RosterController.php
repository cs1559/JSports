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

/**
 * Controller for a single ROSTER ITEM
 *
 */
class RosterController extends FormController
{
    
    public function add() {
        
        
        $model = $this->getModel('Roster','Administrator');
        
        $data = $this->input->post->get('filter', array(), 'array');
        
        $model->programid = $data['programid'];
        $model->teamid = $data['teamid'];
                
        parent::add();
        
    }
    
}