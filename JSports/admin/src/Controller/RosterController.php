<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

/**
 * Controller for a single ROSTER ITEM
 *
 */
class RosterController extends FormController
{
    
    public function add() {
        
        $app = Factory::getApplication();
        $model = $this->getModel('Roster','Administrator');
        
        $data = $this->input->post->get('filter', array(), 'array');
        
        $model->programid = $data['programid'];
        $model->teamid = $data['teamid'];
        
        if (empty($model->teamid)) {
            $app->enqueueMessage(Text::_('COM_JSPORTS_ERR_MISSINGTEAMID'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=rosters', false));
            return false;
        }
        
        
        parent::add();
        
//         // Token check if this is triggered via POST button
//         // if (!Session::checkToken('request')) {
//         //     throw new \RuntimeException('Invalid token', 403);
//         // }
        
//         $app  = Factory::getApplication();
//         $model = $this->getModel('Roster','Administrator');
        
//         $app  = Factory::getApplication();
//         $data = $this->input->post->get('filter', [], 'array');
        
//         $programid = (int) ($data['programid'] ?? 0);
//         $teamid    = (int) ($data['teamid'] ?? 0);
        
//         $model->programid = $data['programid'];
//         $model->teamid = $data['teamid'];
        
//         // Store for the edit form request
//         $app->setUserState('com_jsports.roster.add.programid', $programid);
//         $app->setUserState('com_jsports.roster.add.teamid', $teamid);
                
//         parent::add();
        
    }
    
}