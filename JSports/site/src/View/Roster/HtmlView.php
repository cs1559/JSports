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

namespace FP4P\Component\JSports\Site\View\Roster;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Site\Services\TeamService;
use Joomla\CMS\MVC\View\GenericDataException;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Administrator\Table\RostersTable;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $state;
    
    /**
     * @var RostersTable
     */
    protected $item;
    
    /**
     * @var TeamsTable
     */
    protected $team;
    
    public function display($tpl = null)
    {
//         $input = Factory::getApplication()->input;
        /** @var \FP4P\Component\JSports\Site\Model\RosterModel $model */
        $model = $this->getModel();
        
        $this->item          = $model->getItem();
        $this->state         = $model->getState();
           
        $this->form = $model->getForm($this->item,true);
        $this->form->bind($this->item);       

        $this->team = TeamService::getItem($this->item->teamid);
        
        // Check for errors.
        if (count($errors = $model->getErrors()))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}

