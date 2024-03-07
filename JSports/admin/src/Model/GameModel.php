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

namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Services\TeamService;


class GameModel extends AdminModel
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
    protected function canDelete($record)
    {
        
        if (empty($record->id) || $record->published != -2) {
            return false;
        }
        
        
        if (!empty($record->id)) {
            return $this->getCurrentUser()->authorise('core.delete', 'com_jsports.game.' . (int) $record->id);
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
     * @since   1.6
     */
    protected function canEditState($record)
    {
        // Check for existing article.
        if (!empty($record->id))
        {
            return $this->getCurrentUser()->authorise('core.edit.state', 'com_jsports.game.' . (int) $record->id);
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
     * @since   3.0
     * @throws  \Exception
     */
    public function getTable($name = '', $prefix = '', $options = array())
    {
        $name = 'games';
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
        $form = $this->loadForm('com_jsports.game', 'game', array('control' => 'jform', 'load_data' => $loadData));
        
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
        $data = $app->getUserState('com_jsports.edit.game.data', array());
        
        if (empty($data))
        {
            $data = $this->getItem();
            
            // Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
        }
        
        $this->preprocessData('com_jsports.game', $data);
        
        return $data;
    }  
    
    
    public function save($data) {
     
        $user = Factory::getUser();

        $data['enteredby'] =$user->username;
        
        $datetime = date_create()->format('Y-m-d H:i:s');
        $data['dateupdated'] = $datetime;

        $this->set('programid', $data['programid']);
        $this->set('divisionid', $data['divisionid']);
        $this->set('teamid' ,$data['teamid']);
        
        
        $hometeam = TeamService::getItem($data['teamid']);
        $awayteam = TeamService::getItem($data['opponentid']);
        $data['hometeamid'] = $hometeam->id;
        $data['hometeamname'] = $hometeam->name;
        $data['awayteamid'] = $awayteam->id;
        $data['awayteamname'] = $awayteam->name;
        $data['homeindicator'] = 1;
        
        $data['name'] = $awayteam->name . " @ " . $hometeam->name;
        
        return parent::save($data);
        
//         $table = GameService::getGamesTable();
//         $table->bind($data);
        
//         // Set default values if its a new record.
//         if ($data['id'] == 0) {
//             $table->enteredby = $user->username;
//         }
//         $table->updatedby = $user->username;
//         $datetime = date_create()->format('Y-m-d H:i:s');
//         $table->dateupdated = $datetime;
        
//         // Get Team Names
//         $hometeam = TeamService::getItem($data['teamid']);
//         $awayteam = TeamService::getItem($data['opponentid']);
//         $table->hometeamid = $hometeam->id;
//         $table->hometeamname = $hometeam->name;
//         $table->awayteamid = $awayteam->id;
//         $table->awayteamname = $awayteam->name;
               
//         $table->name = $awayteam->name . " @ " . $hometeam->name;
        
//         $table->check();
        
//         //@TODO Need to add code to catch any error that may exist.
//         if ($table->save($data)) {
//             return true;
//         } else {
//             $errors = $table->getErrors();
//             $this->setError($errors[0]);
//             $app = Factory::getApplication();
//             $app->enqueueMessage($errors[0],'error');
//             return false;
//         }
        
//         return true;
        
    }


}

