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
use FP4P\Component\JSports\Site\Services\SponsorService;

class SponsorAssetsField extends ListField
{
    protected $type = 'SponsorAssets';

    protected function getOptions()
    {
        
        $options = array();
        $imagesonly = false;

        $filterImages   = (string) $this->element['imagesonly'];
        $filterField    = (string) $this->element['filter_field'];

        $filterValue = null;
        
        if ($this->form && $filterField)
        {
            $filterValue = $this->form->getValue($filterField);
        }

        if ($this->form && $filterImages)
        {
		    $imagesonly = filter_var($filterImages, FILTER_VALIDATE_BOOLEAN);
        }
        
        $items = SponsorService::getAssets($filterValue, $imagesonly);

        foreach ($items as $item) {
            $options[] = HtmlHelper::_('select.option',$item->id,$item->title . ' (' . $item->sponsorname .')');
        }
           
        $options = array_merge(parent::getOptions(), $options);
                
        return $options;
        
    }
        
}