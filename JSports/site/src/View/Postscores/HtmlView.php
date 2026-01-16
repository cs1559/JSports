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

namespace FP4P\Component\JSports\Site\View\Postscores;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Site\Services\SecurityService;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Pagination\Pagination;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Administrator\Table\ProgramsTable;

/**
 * PostScores HTML View - used by clients to see a list of games they can post a score of.
 * 
 * @author Chris Strieter
 *
 */
class HtmlView extends BaseHtmlView
{
    public $form;
    
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;
    
    /**
     * @var TeamsTable
     */
    protected $team;
    /**
     * @deprecated
     * @var int
     */
    protected $teamlastyearplayed;
    
    /**
     * @var ProgramsTable
     */
    protected $program;
    protected $canEdit = false;
      
    protected $state;
          
    public function display($tpl = null)
    {
        /** @var \FP4P\Component\JSports\Site\Model\PostscoresModel */
        $model = $this->getModel();
        
        $this->items        = $model->getItems();
        $this->team         = $model->getTeam();
        $this->program      = $model->getProgram();
        $this->canEdit      = SecurityService::canEditTeamSchedule($this->team->id,$this->program->id);
        //$this->canEdit = SecurityService::canEditTeamSchedule($context);

        if ($this->program->registrationonly) {
            $this->setLayout("unavailable");
        }
        if ($this->program->status == "C") {
            $this->setLayout("unavailable");
        }
        if (!$this->program->setupfinal) {
            $this->setLayout("unavailable");
        }
        
        // Check for errors.
        if (count($errors = $model->getErrors()))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}

