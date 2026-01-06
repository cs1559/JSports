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
        
        /* NOTE:  This list of teams will/should be filtered based on the division ID assocaited with the team id passed into this field */
        
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
        
        $options[] = HtmlHelper::_('select.option',"0","-- Select Team --");
        
        /* Load the dropdown with teams from their assigned division */
        foreach ($teams as $item) {
            $text = $item['teamname'] . ' (' . $item['contactname'] . ')';
            //$options[] = HtmlHelper::_('select.option',$item['teamid'],$item['teamname']);
            $options[] = HtmlHelper::_('select.option',$item['teamid'],$text);
        }
        
        $divisionid = TeamService::getTeamDivisionId($this->teamid, $this->programid);
        $division = DivisionService::getItem($divisionid);
        
        if ($division->crossdivisional) {
           // $options[] = Html::_('select.optgroup',"","Outside Divisions");
        	$obj = new \stdClass();
        	$obj->value ="<OPTGROUP>";
        	$obj->text="Outside Divisions";
       		$options[] = $obj;

       		
       		// get divisions within age group.
       		$divisions = DivisionService::getDivisionList($this->programid, $division->agegroup, $divisionid);
       		
//        		print_r($divisions);
//        		exit;
       		
       		// Loop through divisions
       		foreach ($divisions as $div) {
       		   // Loop through teams within the division
       		    $obj = new \stdClass();
       		    $obj->value ="<OPTGROUP>";
       		    $obj->text=$div['name'] . " Division";
       		    $options[] = $obj;
       		    
              		    
//                 $teams = Teamservice::getTeamsByAgeGroup($this->programid, $division->agegroup, $division->id);
       		    $teams = Teamservice::getTeamlist2($this->programid, $div['id']);
                foreach ($teams as $item) {
                    $text = $item['teamname'] . ' (' . $item['contactname'] . ')';
                    //$options[] = HtmlHelper::_('select.option',$item['teamid'],$item['teamname']);
                    $options[] = HtmlHelper::_('select.option',$item['teamid'],$text);
                }
                
                // "Div optgroup"
                $optGroup              = new \stdClass();
                $optGroup->value       = '</OPTGROUP>';
                $optGroup->text        = "";
                $options[] = $optGroup;
                
       		}
       		
            // "Outside divisions optgroup"
            $optGroup              = new \stdClass();
            $optGroup->value       = '</OPTGROUP>';
            $optGroup->text        = "";
            $options[] = $optGroup;
        }
        
        $options = array_merge(parent::getOptions(), $options);
        
        
        return $options;
        
    }
        

}