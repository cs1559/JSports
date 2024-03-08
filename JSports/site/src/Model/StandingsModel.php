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

/**
 * 
 *
 * @since  1.6
 */
class StandingsModel extends BaseModel
{
    
    protected $standings;
    protected $program;
    
    /**
     * This function will retrieve a list of teams assocaited with the user.
     * 
     * @return unknown
     */
    public function getProgramStandings(){
  
        $input = Factory::getApplication()->input;
        $programid = (int) $input->get('programid');
        
        $this->program = ProgramsService::getItem($programid);
        
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
    
    
    
}