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