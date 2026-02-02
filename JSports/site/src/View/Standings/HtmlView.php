<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\View\Standings;

defined('_JEXEC') or die;

use FP4P\Component\JSports\Administrator\Table\ProgramsTable;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * HTML Standings View
 *
 */
class HtmlView extends BaseHtmlView
{

    /**
     * The standings variable is an array based on a query executed in the StandingsService.  It can
     * pull information that from the primary standings table or a past standings table.
     * 
     * @var array<int, array<string, mixed>>
     */
    protected $standings;
    
    /**
     * @var ProgramsTable
     */
    protected $program;
    
    /**
     * This is an array of divisions for a given program.
     * @var array<int, array<string, mixed>> | null
     */
    protected $divisions;

    
    /**
     * Execute and display a template script.   This will check the the program settings to see what template to use.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        /** @var \FP4P\Component\JSports\Site\Model\StandingsModel $model */
        $model = $this->getModel();
       
        //        $input = Factory::getApplication()->input;
        //         $programid = (int) $input->get('programid');
        
        $this->standings = $model->getProgramStandings();
        $this->program = $model->getProgram();
        $this->divisions = $model->getDivisions();
 
//         if (!$this->program->publishstandings) {
//             //$this->setLayout("nonpublished");
//         }
        
        return parent::display($tpl);
//             $this->program          = $model->getProgram();
            
//             if (!$this->program) {
//                 $this->setLayout('unavailable');
//                 return parent::display($tpl);
//             }
            
//             $this->standings        = $model->getProgramStandings();
//             $this->divisions        = $model->getDivisions();
            
//             if (!$this->program->publishstandings) {
//                  //$this->setLayout("nonpublished");
//             }
            
//             return parent::display($tpl);
            
        
    }
}