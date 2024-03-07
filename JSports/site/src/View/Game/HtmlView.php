<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\View\Game;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Helpers\Html;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;

use Joomla\CMS\Form\Formhelper;

//FormHelper::loadFieldClass('list');

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
    protected $canEdit = false;
    
    
//     /**
//      * The pagination object
//      *
//      * @var  \JPagination
//      */
//     protected $pagination;
    
    /**
     * The model state
     *
     * @var  \JObject
     */
    protected $state;
    
    public function display($tpl = null)
    {        
        $input = Factory::getApplication()->input;
        $itemid = $input->get('id',0);
        
        $this->item         = $this->get('Item');
        $this->state         = $this->get('State');
        
//         $this->team = $this->get('team') ;   
        
        $this->form = $this->getModel()->getForm($this->item,true);
        $this->form->bind($this->item);
        
        // NOTE:  Need to research to see if there is a better way of getting the model data into the template
        $mod = $this->getModel();
        
        
        $this->team = $mod->team;
        $this->program = $mod->program;
        $this->teamid = $mod->teamid;
        $this->programid = $mod->programid;


        if ($itemid == 0) {
            $this->form->setValue('teamid',null,$mod->teamid);
            $this->form->setValue('programid',null,$mod->programid);
            $this->form->setValue('divisionid',null,$mod->divisionid);
        }
        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
                
        return parent::display($tpl);
        
    }
       
}