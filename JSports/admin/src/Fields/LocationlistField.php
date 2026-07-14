<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace FP4P\Component\JSports\Administrator\Fields;

defined('_JEXEC') or die;
    

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
// use Joomla\CMS\Form\FormFieldList;
use Joomla\CMS\Form\Formhelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Field\ComboField;
use Joomla\CMS\HTML\HTMLHelper;
use FP4P\Component\JSports\Administrator\Helpers\Html;
use FP4P\Component\JSports\Site\Services\TeamService;   
use Joomla\Database\DatabaseInterface;


class LocationlistField extends ComboField
{
    protected $type = 'Locationlist';
    
    protected function getOptions()
    {
        
        $options = array();
        
//         $db    = Factory::getDbo();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
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