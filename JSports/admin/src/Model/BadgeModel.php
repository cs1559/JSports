<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\Database\ParameterType;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
class BadgeModel extends AdminModel
{
    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
//     protected function canDelete($record)
//     {
        
//            if (empty($record->id) || $record->state != -2) {
//                 return false;
//             }
            
//             if (!empty($record->id)) {
//                 return $this->getCurrentUser()->authorise('core.delete', 'com_jsports.venue.' . (int) $record->id);
//             }
            
//             return parent::canDelete($record);
//     }
        
        
    
//     /**
//      * Method to test whether a record can have its state edited.
//      *
//      * @param   object  $record  A record object.
//      *
//      * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
//      *
//      * @since   1.6
//      */
//     protected function canEditState($record)
//     {
//         // Check for existing article.
//         if (!empty($record->id))
//         {
//             return $this->getCurrentUser()->authorise('core.edit.state', 'com_jsports.venue.' . (int) $record->id);
//         }
        
//         // Default to component settings if neither article nor category known.
//         return parent::canEditState($record);
//     }
    
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     *
     * @since   3.0
     * @throws  \Exception
     */
    public function getTable($name = '', $prefix = '', $options = array())
    {
        $name = 'badges';
        $prefix = 'Table';
        
        if ($table = $this->_createTable($name, $prefix, $options))
        {
            return $table;
        }
        
        throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
    }
    
    
    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  Form|boolean  A Form object on success, false on failure
     *
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_jsports.badge', 'badge', array('control' => 'jform', 'load_data' => $loadData));
        
        if (empty($form))
        {
            return false;
        }
        
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_jsports.edit.badge.data', array());
        
        if (empty($data))
        {
            $data = $this->getItem();
            
            // Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
        }
        
        $this->preprocessData('com_jsports.badge', $data);
        
        return $data;
    }

    /**
     * Method to prepare a Table object before it is stored, in order to
     * derive the alias from the name when the alias field is left blank.
     *
     * @param   Table  $table  A Table object.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function prepareTable($table)
    {
        // If an alias was supplied, still run it through the URL-safe filter
        // in case it contains spaces, punctuation, etc.
        if (!empty($table->alias))
        {
            $table->alias = ApplicationHelper::stringURLSafe($table->alias);
        }

        // No alias supplied at all — derive one from the name, same as core content items.
        if (empty($table->alias) && !empty($table->name))
        {
            $table->alias = ApplicationHelper::stringURLSafe($table->name);
        }

        // Guard against an alias that filters down to nothing (e.g. punctuation-only name).
        if (trim(str_replace('-', '', (string) $table->alias)) === '')
        {
            $table->alias = Factory::getDate()->format('Y-m-d-H-i-s');
        }
    }
}