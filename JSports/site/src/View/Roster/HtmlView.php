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

namespace FP4P\Component\JSports\Site\View\Roster;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
//use FP4P\Component\JSports\Administrator\Table\LeaguesTable;
use FP4P\Component\JSports\Site\Services\TeamService;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;

class HtmlView extends BaseHtmlView
{
    public $form;
    
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;
    
    protected $team;
    protected $teamlastyearplayed;
    protected $program;
    protected $canEdit = false;
        
    /**
     * The model state
     *
     * @var  \JObject
     */
    protected $state;
    
    public function display($tpl = null)
    {
        $input = Factory::getApplication()->input;
        
        $this->item         = $this->get('Item');
        $this->state         = $this->get('State');
        
        $this->form = $this->getModel()->getForm($this->item,true);
        $this->form->bind($this->item);       

        $this->team = TeamService::getItem($this->item->teamid);
        
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}

