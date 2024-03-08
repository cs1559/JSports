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
use FP4P\Component\JSports\Site\Services\DivisionService;


class TeamlistField extends ListField
{
    protected $type = 'Teamlist';
    protected $teamid = 0;
    protected $programid = 0;
    
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $result = parent::setup($element, $value, $group);
        
        $form = $this->form;
        $this->teamid = $form->getValue('teamid');
        $this->programid = $form->getValue('programid');
        return $result;
        
    }
    
    protected function getOptions()
    {
        
        $options = array();
        
//         $teamkey = (string) $this->element['teamid'];
        if ($this->teamid == 0) {
            $options[] = HtmlHelper::_('select.option','','MISSING TEAM PARAMETER');
            return $options;
        }
        
//         $programkey = (string) $this->element['programid'];
        if ($this->programid == 0) {
            $options[] = HtmlHelper::_('select.option','','MISSING PROGRAM PARAMETER');
            return $options;
        }
        
        $teams = TeamService::getTeamList($this->teamid, $this->programid);
        
        /*
         * 
         *   [0]=>
  object(stdClass)#1102 (3) {
    ["value"]=>
    string(0) ""
    ["text"]=>
    string(17) "-- Select Team --"
    ["disable"]=>
    bool(false)
  }
  
         */
        
        $options[] = HtmlHelper::_('select.option',"","-- Select Team --");
        foreach ($teams as $item) {
            $text = $item['teamname'] . ' (' . $item['contactname'] . ')';
            //$options[] = HtmlHelper::_('select.option',$item['teamid'],$item['teamname']);
            $options[] = HtmlHelper::_('select.option',$item['teamid'],$text);
        }
        
        $divisionid = TeamService::getTeamDivisionId($this->teamid, $this->programid);
        $division = DivisionService::getItem($divisionid);
        


        if ($division->crossdivisional) {
           // $options[] = Html::_('select.optgroup',"","Outside Division");
        	$obj = new \stdClass();
        	$obj->value ="<OPTGROUP>";
        	$obj->text="Outside Divisions";
       		$options[] = $obj;

            $teams = Teamservice::getTeamsByAgeGroup($this->programid, $division->agegroup, $division->id);
            foreach ($teams as $item) {
                $text = $item['teamname'] . ' (' . $item['contactname'] . ')';
                //$options[] = HtmlHelper::_('select.option',$item['teamid'],$item['teamname']);
                $options[] = HtmlHelper::_('select.option',$item['teamid'],$text);
            }

                $optGroup              = new \stdClass();
                $optGroup->value       = '</OPTGROUP>';
                $optGroup->text        = "";
		$options[] = $optGroup;
        }
        
        $options = array_merge(parent::getOptions(), $options);
        
        
        return $options;
        
    }
        

}