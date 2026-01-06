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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Objects\Standings\StandingsEngine;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Router\Route;

class ProgramsController extends AdminController
{
    protected $default_view = 'programs';
    
    public function getModel($name = 'Program', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
 
    public function close() {
        
        $this->checkToken();
        
        // @TODO   Refactor this code to return a single ID and eliminate use of $ids[0]
        $ids = $this->input->post->get('cid', array(), 'array');
        $ids = array_values(array_filter(array_map('intval', $ids)));
        
        if (count($ids) > 1) {
            $this->app->enqueueMessage("You can only close one program at a time", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs',false));
            return false;
        } else {
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=closeprogram&id=' . $ids[0], false));
            return true;
        }
    }
    
    public function refreshstandings(){
        
        $this->checkToken();
        
        // @TODO   Refactor this code to return a single ID and eliminate use of $ids[0]
        $ids = $this->input->post->get('cid', array(), 'array');
        $ids = array_values(array_filter(array_map('intval', $ids)));
        
        if (count($ids) > 1) {
            $this->app->enqueueMessage("You can only REFRESH STANDINGS one program at a time", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs',false));
            return false;
        } 
        
        $program = ProgramsService::getItem($ids[0]);

        if ($program->registrationonly) {
            $this->app->enqueueMessage("REFRESH STANDINGS does not apply to REGISTRATION ONLY programs", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs',false));
            return false;
        }
        
        if ($program->status != 'A') {
            $this->app->enqueueMessage("You can only REFRESH STANDINGS on an ACTIVE program", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs',false));
            return false;
        }


        $engine = new StandingsEngine();
        
        if ($engine->generateStandings($program->id)) {
//             Factory::getApplication()->enqueueMessage("Standings Refreshed", 'message');
            $this->app->enqueueMessage("Standings Refreshed", 'message');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs',false));
            return true;
        } else {
            $this->app->enqueueMessage("There was an error when refreshing the standings", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs', false));
            return false;
        }
        
    }
    
    
    /**
     * Logic when the user selects 'SETUP' from the programs list.  
     */
    public function setup() {
        
        $this->checkToken();
        
        $app = $this->app;
        
        // @TODO   Refactor this code to return a single ID and eliminate use of $ids[0]
        $ids = $this->input->post->get('cid', array(), 'array');
        $ids = array_values(array_filter(array_map('intval', $ids)));
        
        if (count($ids) > 1) {
            $this->app->enqueueMessage("You can only SETUP one program at a time", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs',false));
            return false;
        } 

        $program = ProgramsService::getItem($ids[0]);
        
        if ($program->registrationonly) {
            $this->app->enqueueMessage("Setup does not apply to REGISTRATION ONLY programs", 'warning');
            $this->setRedirect(Route::_('index.php?option=com_jsports&view=programs'));
            return false;
        }
        
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=programsetup&programid=' . $ids[0], false));
        return true;
    }
    
    
}
