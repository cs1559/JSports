<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 */
namespace FP4P\Component\JSports\Administrator\Fields;

defined('_JEXEC') or die;
    
// use Joomla\CMS\Form\FormFieldList;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use FP4P\Component\JSports\Site\Services\SponsorService;

class ActivesponsorsField extends ListField
{
    protected $type = 'Activesponsors';

    protected function getOptions()
    {
        
        $options = array();
                     
        $items = SponsorService::getActiveSponsors();
                
        $options[] = HtmlHelper::_('select.option',"","-- Select Sponsor --");
        foreach ($items as $item) {
            $options[] = HtmlHelper::_('select.option',$item->id,$item->name);
        }
           
        $options = array_merge(parent::getOptions(), $options);
                
        return $options;
        
    }
        
}