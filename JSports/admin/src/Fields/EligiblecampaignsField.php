<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Administrator\Fields;

defined('JPATH_PLATFORM') or die;
    
// use Joomla\CMS\Form\FormFieldList;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use FP4P\Component\JSports\Site\Services\CampaignService;

class EligiblecampaignsField extends ListField
{
    protected $type = 'Eligiblecampaigns';

    protected function getOptions()
    {
        
        $options = array();
        
        $position = (string) $this->element['positioins'];
                     
        $items = CampaignService::getEligibleCampaigns($position);
                
        $options[] = HtmlHelper::_('select.option',"","-- Select Campaign --");
        foreach ($items as $item) {
            $options[] = HtmlHelper::_('select.option',$item->id,$item->title);
        }
           
        $options = array_merge(parent::getOptions(), $options);
                
        return $options;
        
    }
        
}