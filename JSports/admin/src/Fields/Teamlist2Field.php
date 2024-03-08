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
namespace FP4P\Component\JSports\Administrator\Fields;

defined('JPATH_PLATFORM') or die;
    
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormFieldList;
use Joomla\CMS\Form\Formhelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use FP4P\Component\JSports\Administrator\Helpers\Html;
use FP4P\Component\JSports\Site\Services\TeamService;


class Teamlist2Field extends ListField
{
    protected $type = 'Teamlist2';
    protected $teamid = 0;
    protected $programid = 0;
    
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $result = parent::setup($element, $value, $group);
        
        $form = $this->form;
        $this->programid = $form->getValue('programid');
        $this->divisionid = $form->getValue('divisionid');
        return $result;
        
    }
    
    protected function getOptions()
    {
        
        $options = array();
        
        if ($this->divisionid == 0) {
//             $options[] = HtmlHelper::_('select.option','','MISSING DIVISIONID PARAMETER');
            $options[] = HtmlHelper::_('select.option',"","-- Select Team --");
            return $options;
        }
        
//         if ($this->programid == 0) {
//             $options[] = HtmlHelper::_('select.option','','MISSING PROGRAM PARAMETER');
//             return $options;
//         }
        
        $teams = TeamService::getTeamList2($this->programid, $this->divisionid);
                
        $options[] = HtmlHelper::_('select.option',"","-- Select Team --");
        foreach ($teams as $item) {
            $options[] = HtmlHelper::_('select.option',$item['teamid'],$item['teamname'] .'('  . $item['contactname']. ')');
        }
        
           
        $options = array_merge(parent::getOptions(), $options);
        
        
        return $options;
        
    }
        

}