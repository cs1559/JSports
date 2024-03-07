<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
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
use Joomla\CMS\Form\Field\ComboField;
use Joomla\CMS\HTML\HTMLHelper;
use FP4P\Component\JSports\Administrator\Helpers\Html;
use FP4P\Component\JSports\Site\Services\TeamService;


class LocationlistField extends ComboField
{
    protected $type = 'Locationlist';
    
    protected function getOptions()
    {
        
        $options = array();
        
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select($db->quoteName(array('id', 'name','city','state')));
        $query->from($db->quoteName('#__jsports_venues'));
        
        $conditions = array(
            $db->quoteName('published') . ' = 1',
        );
        
        $query->where($conditions);
        $db->setQuery($query);
        
        $items = $db->loadAssocList();
         
        $options[] = HtmlHelper::_('select.option',"","-- Select Location --");
        foreach ($items as $item) {
            $fullname = $item['name'] . " (" . $item['city'] . ' ' . $item['state'] . ")";
            $options[] = HtmlHelper::_('select.option',$item['id'],$fullname);
        }
        
           
        $options = array_merge(parent::getOptions(), $options);
        
        
        return $options;
        
    }
        

}