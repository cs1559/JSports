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
use FP4P\Component\JSports\Site\Services\ProgramsService;

class ProgramslistField extends ListField
{
    protected $type = 'Programslist';
    protected $teamid = 0;
    protected $programid = 0;
    
    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $result = parent::setup($element, $value, $group);
        
        $form = $this->form;
        $this->programid = $form->getValue('programid');
        return $result;
        
    }
    
    protected function getOptions()
    {
        
        $options = array();
        $registrationonly = (string) $this->element['registrationonly'];
                     
        $items = ProgramsService::getProgramList();
                
        $options[] = HtmlHelper::_('select.option',"","-- Select Program --");
        foreach ($items as $item) {
            switch ($registrationonly) {
                case "true":
                    $options[] = HtmlHelper::_('select.option',$item['id'],$item['name']);
                    break;
                default:
                    if (!$item['registrationonly']) {
                        $options[] = HtmlHelper::_('select.option',$item['id'],$item['name']);
                    }
                    
                    break;
            }
        }
           
        $options = array_merge(parent::getOptions(), $options);
                
        return $options;
        
    }
        
}