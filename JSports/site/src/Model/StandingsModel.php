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
use FP4P\Component\JSports\Site\Services\StandingsService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\DivisionService;

/**
 *  The StandingsModel class is used to retrieve the standings informaiton for a particular program.
 *
 */
class StandingsModel extends BaseModel
{
    
    protected $standings;
    protected $program;
    protected $divisions;
    
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState($ordering, $direction);
        
        $app = Factory::getApplication();
        $input = $app->input;
        
        $this->setState('standings.programid', $input->getInt('programid'));
    }
    
    /**
     * This function will return the standings for a given program.
     *
     * @return array
     */
    public function getProgramStandings(){
  
        $programid ??= (int) $this->getState('standings.programid', 0);
        
//         $input = Factory::getApplication()->input;
//         $programid = (int) $input->get('programid');
        
        if ($programid < 1) {
            $program = ProgramsService::getDefaultProgram();
            $programid = $program->id;
        }
        $this->program = ProgramsService::getItem($programid);
        $this->divisions = DivisionService::getDivisionList($programid);
        
        if ($this->program->status == 'C') {
            $this->standings = StandingsService::getProgramStandings($programid,true);
        } else {
            $this->standings = StandingsService::getProgramStandings($programid);
        }
        return $this->standings;
        
    }
    
    public function getProgram() {
        return $this->program;
    }
    
    public function getDivisions() : array {
        return $this->divisions ?? [];
    }
    
    
}