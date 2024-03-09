<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;

use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Objects\stats\TotalTeams;
use FP4P\Component\JSports\Site\Objects\stats\GameStatistics;


/**
 * 
 *
 */
class DashboardModel extends BaseModel
{

    public $context = array();
    public $totalteams = 0;
    public $programs;
    public $totalgames;
    public $gamesscheduled;
    public $gamescompleted;
    public $gamesother;
    public $upcominggames;
    
    
    public function __construct($config = []) {
        
        $this->context = $this->getContext();
        
        parent::__construct($config);
    }
    
    private function getContext() {
               
        return $this->context;
    }
    
    public function setContext(array $context) {
            $this->context = $context;
    }
    
    public function getPrograms() {
    
        $context = $this->getContext();
        $programs = ProgramsService::getNonCompletedPrograms();
        $games = GameService::getRecentGames($context['programid'],10);
        $this->programs = $programs;
    }
    
    
    /**
     * This function retrieves various statistics for a program.
     * @return \FP4P\Component\JSports\Site\Model\DashboardModel
     */
    public function getStatistics() {

	   $context = $this->getContext();
       $this->totalteams = $this->getTotalTeams($context);
        
       $gamestats = $this->getGameStatistics($context);
       if (array_key_exists('totalgames', $gamestats)) {
           $this->totalgames = $gamestats['totalgames'];
       }
       if (array_key_exists('gamesscheduled', $gamestats)) {
           $this->gamesscheduled = $gamestats['gamesscheduled'];
       }
       if (array_key_exists('gamescompleted', $gamestats)) {
           $this->gamescompleted = $gamestats['gamescompleted'];
       }
       if (array_key_exists('gamesother', $gamestats)) {
           $this->gamesother = $gamestats['gamesother'];
       }
       
       return $this;
        
    }
    
    public function getTotalTeams($context) {
        
        $obj = new TotalTeams($context);
        $total = $obj->getData();
        return $total;
    }
    
    public function getGameStatistics($context)
    {
        $obj = new GameStatistics($context);
        $result = $obj->getData();
        return $result;
        
    }
    
    public function getUpcomingGames() {
        
        $context = $this->getContext();
        $programid = 0;
        $limit = 20;
        if (array_key_exists('programid', $context)) {
            $programid = $context['programid'];
        }
        if (array_key_exists('limit', $context)) {
            $limit = $context['limit'];
        }
        $games = GameService::getUpcomingGames($programid, $limit);
        return $games;    
        
               
    }
}