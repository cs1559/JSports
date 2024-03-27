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

class ProgramsController extends AdminController
{
    protected $default_view = 'programs';
    
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }

    
    public function publish() {
        $model = $this->getModel('Program');
        
        parent::publish();
    }

    public function getModel($name = 'Program', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
 
    public function close() {
        $ids = $this->input->post->get('cid', array(), 'array');
        if (count($ids) > 1) {
            Factory::getApplication()->enqueueMessage("You can only close one program at a time", 'warning');
            $this->setRedirect('index.php?option=com_jsports&view=programs');
        } else {
            $this->setRedirect('index.php?option=com_jsports&view=closeprogram&id=' . $ids[0]);
        }
    }
    
    public function refreshstandings(){
        $ids = $this->input->post->get('cid', array(), 'array');
        if (count($ids) > 1) {
            Factory::getApplication()->enqueueMessage("You can only REFRESH STANDINGS one program at a time", 'warning');
            $this->setRedirect('index.php?option=com_jsports&view=programs');
            return;
        } 
        
        $program = ProgramsService::getItem($ids[0]);

        if ($program->registrationonly) {
            Factory::getApplication()->enqueueMessage("REFRESH STANDINGS does not apply to REGISTRATION ONLY programs", 'warning');
            $this->setRedirect('index.php?option=com_jsports&view=programs');
            return;
        }
        
        if ($program->status != 'A') {
            Factory::getApplication()->enqueueMessage("You can only REFRESH STANDINGS on an ACTIVE program", 'warning');
            $this->setRedirect('index.php?option=com_jsports&view=programs');
            return;
        }


        $engine = new StandingsEngine();
        
        if ($engine->generateStandings($program->id)) {
            Factory::getApplication()->enqueueMessage("Standings Refreshed", 'message');
            $this->setRedirect('index.php?option=com_jsports&view=programs');
        } else {
            Factory::getApplication()->enqueueMessage("There was an error when refreshing the standings", 'warning');
            $this->setRedirect('index.php?option=com_jsports&view=programs');
        }
        
    }
}
