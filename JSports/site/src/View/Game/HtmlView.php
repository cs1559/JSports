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

namespace FP4P\Component\JSports\Site\View\Game;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;

/**
 * HTML GAME View
 *
 * - 01/15/2026 - Refactored to change $this->get to $model->getxxx to commply with future Joomla releases.
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    public $form;
    protected $item;
    
    /** @var TeamsTable */
    protected $team;
    
    protected $canEdit = false;    
    protected $state;
    
//     protected $program;
    
    /** @var int $teamid  Maps to team id */
    protected $teamid;
    
    /** @var int $contextid  Maps to team id */
    protected $contextid;
    
    public function display($tpl = null)
    {
        $input = Factory::getApplication()->input;
        $itemid = $input->get('id',0);
        
        /** @var \FP4P\Component\JSports\Site\Model\GameModel $model */
        $model = $this->getModel();
        
        $this->item             = $model->getItem();
        $this->state            = $model->getState();
        $this->form             = $model->getForm($this->item,true);
        $this->form->bind($this->item);
              
        $this->team             = $model->team;
//         $this->program          = $model->program;
        $this->teamid           = $model->teamid;
        $this->programid        = $model->programid;
        $this->contextid        = $model->contextid;
        
        $this->form->setValue('contextid',null, $model->contextid);

        if ($itemid == 0) {
            $this->form->setValue('teamid',null,$model->teamid);
            $this->form->setValue('programid',null,$model->programid);
            $this->form->setValue('divisionid',null,$model->divisionid);
        }
        
        // Check for errors.
        $errors = $model->getErrors();
        if (!empty($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
    
       
}

