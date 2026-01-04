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

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

class DivisionModel extends AdminModel
{
    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     */
    protected function canDelete($record)
    {
        
           if (empty($record->id) || $record->state != -2) {
                return false;
            }
            
            if (!empty($record->id)) {
                return $this->getCurrentUser()->authorise('core.delete', 'com_jsports.division.' . (int) $record->id);
            }
            
            return parent::canDelete($record);
    }
        
        
    
    /**
     * Method to test whether a record can have its state edited.
     *
     * @param   object  $record  A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     */
    protected function canEditState($record)
    {
        // Check for existing article.
        if (!empty($record->id))
        {
            return $this->getCurrentUser()->authorise('core.edit.state', 'com_jsports.division.' . (int) $record->id);
        }
        
        // Default to component settings if neither article nor category known.
        return parent::canEditState($record);
    }
    
    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     *
     * @throws  \Exception
     */
    public function getTable($name = '', $prefix = '', $options = array())
    {
        $name = 'divisions';
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
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        
        $form = $this->loadForm('com_jsports.division', 'division', array('control' => 'jform', 'load_data' => $loadData));
        
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
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_jsports.edit.division.data', []);
        
        if (empty($data))
        {
            $data = $this->getItem();         
        }
        
        $this->preprocessData('com_jsports.division', $data);
        
        return $data;
    }
}
