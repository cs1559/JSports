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
use Joomla\CMS\MVC\Model\ListModel;
use FP4P\Component\JSports\Administrator\Services\RegistrationService;
use Joomla\CMS\Filter\OutputFilter;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\MailService;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;


class RegistrationModel extends AdminModel
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
                return $this->getCurrentUser()->authorise('core.delete', 'com_jsports.registration.' . (int) $record->id);
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

        // You cannot "unpublish" a registration record once it's been published.  This is because of all the actions that take place to setup
        // the team, etc. in the database.
        
        if ($record->published > 0) {
            return false;
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
        $name = 'registrations';
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
        $form = $this->loadForm('com_jsports.registration', 'registration', array('control' => 'jform', 'load_data' => $loadData));
        
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
        $data = $app->getUserState('com_jsports.edit.registration.data', array());
        
        if (empty($data))
        {
            $data = $this->getItem();
            
            // Pre-select some filters (Status, Category, Language, Access) in edit form if those have been selected in Article Manager: Articles
        }
        
        $this->preprocessData('com_jsports.registration', $data);
        
        return $data;
    }
    

    /**
     * Method to change the published state of one or more records.
     *
     * @param   array    &$pks   A list of the primary keys to change.
     * @param   integer  $value  The value of the published state.
     *
     * @return  boolean  True on success.
     *
     * @since   4.0.0
     */
    public function publish(&$pks, $value = 1) {
        
        if ($value > 0) {
            $return = true;
            
            foreach ($pks as $id) {
                $rc = $this->processRegistration($id);
                if (!$rc) {
                    $return = false;
                }
            }
            return $return;
        } 
            
         return parent::publish($pks, $value);
    }
    
    public function unpublish(&$pks, $value = 1) {
        
        if ($value > 0) {
            return false;
        }
        
        return parent::unpublish($pks, $value);
    }
    
    
    /**
     * This function performs the business logic to process/publish a given registration.  This process will do the following:
     *
     * 1.  Create a new team record if appropriate
     * 2.  Add the record to the Team/Program/Division mapping file.
     * 3.  Publish the registration record.  Once its published, it cannot be published again.
     *
     * @param unknown $regid
     * @return boolean
     */
    protected function processRegistration($regid) {
        
        $db = $this->getDatabase();
        
        $rsvc = new RegistrationService();
        $item = $rsvc->getItem($regid);
        
        if ($item->published) {
            Factory::getApplication()->enqueueMessage("State of a published registration cannot be changed", 'error');
            return false;
        }
        try {
            
            if ($item->teamid) {
                $team = TeamService::getitem($item->teamid);
                if (is_null($item)) {
                    $createteam = true;
                } else {
                    $createteam = false;
                }
            } else {
                $createteam = true;
            }
            
            // begin transactio
            $db->transactionStart();
            
            if ($createteam) {
                // =========================================================================================
                // Create Team record
                // =========================================================================================
                $query = $db->getQuery(true);
                $columns = array(
                    'id',
                    'name',
                    'alias',
                    'city',
                    'state',
                    'contactname',
                    'contactemail',
                    'contactphone',
                    'published'
                );
                $teamname = OutputFilter::stringURLUnicodeSlug($item->teamname);
                
                $values = array(
                    0,
                    $db->quote($item->teamname), // teamname
                    $db->quote($teamname), // alias
                    $db->quote($item->city), // city
                    $db->quote($item->state), // state
                    $db->quote($item->name), // contact name
                    $db->quote($item->email), // contact email
                    $db->quote($item->phone), // contact phone
                    1 // published
                );
                
                $query->insert($db->quoteName('#__jsports_teams'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));
                
                $db->setQuery($query);
                $db->execute();
                
                // Get the row that was just inserted
                $new_row_id = $db->insertid();
            } else {
                $new_row_id = $item->teamid;
            }
            
            $query = $db->getQuery(true);
            // =========================================================================================
            // Create Mapping Record - need newly created teamdid, programid, regid (set divid to 0)
            // =========================================================================================
            $columns = array(
                'id',
                'programid',
                'teamid',
                'divisionid',
                'regid',
                'published'
            );
            $values = array(
                0,
                $db->quote($item->programid), // programid (from registration record)
                $db->quote($new_row_id), // team id - determined by previous insert into team table
                $db->quote(0), // divisionid - set to zero (0) as divisional assignment has not been made yet
                $db->quote($item->id), // Registration ID
                0 // published - SET TO ZERO (0)
            );
            
            $query->insert($db->quoteName('#__jsports_map'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
            
            
            // =========================================================================================
            //  Set registration record to published.
            // =========================================================================================
            $query = $db->getQuery(true);
            // Set fields to update on the registration record
            if ($createteam) {
                $fields = array($db->quoteName('published') . ' = 1', $db->quoteName('teamid') . ' = ' . $new_row_id);
            } else {
                $fields = array($db->quoteName('published') . ' = 1');
            }

            $conditions = array($db->quoteName('id') . ' = ' .$item->id);
            
            $query->update($db->quoteName('#__jsports_registrations'))
            ->set($fields)
            ->where($conditions);
            $db->setQuery($query);
            $db->execute();
            
            // commit transaction
            $db->transactionCommit();
        
            $this->sendProcessedNotification($new_row_id, $item);
            
        } catch (Exception $e) {
            // // catch any database errors.
            $db->transactionRollback();
            Factory::getApplication()->enqueueMessage("Registration publish failed for one or more registration records", 'error');
            return false;
            
        }
        return true;
    }
    

    private function sendProcessedNotification($teamid, $item) {
        
        $params = ComponentHelper::getParams('com_jsports');
        $ccadmin = $params->get('ccadmin');
        
        $teamname = OutputFilter::stringURLUnicodeSlug($item->teamname);
        
        $uri = Uri::getInstance();
        
        $body = "
<p>Your are receiving this email because your email address was associated with a team registration (ID: " . $item->id . ") for the upcoming SWIBL season.  Your registration 
has been processed.  This means your team profile should now be available.  You can view your team profile by clicking  
<a href='" . $uri->root() . "/baseball/team/" . $teamid . "-" . $teamname . "'>HERE</a>.
</p>
<p>
A team's profile is where all data is entered.  This includes game schedules, rosters, team contacts, etc.   Each profile will need an administrator to 
manage all of the team related data.  If you are a returning team and a new profile was not created, then your existing admin will continue to have 
permission to edit the information.  If you are new to SWIBL, someone from your team will need to create an account on the SWIBL website and email the league
indicating your name, role and SWIBL username.  This information will be used to grant your SWIBL account permissions to edit/maintain your specific teams 
profile. 
</p>
<p>   
<br/>
If you have any questions regarding this email, please email the league and we'll respond as soon as possible.<br/>
<br/>
SWIBL<br/>
Email: info@swibl.org<br/>
</p> ";
        
        
        // http://localhost:8081/baseball/team/1224
        
        $subject = "SWIBL - Team Profile Available (" . $item->teamname . ')';
        
        $adminrecipients = array();
        if ($ccadmin) {
            //             echo "email the admins";
            //             echo $adminemails;
            if ($multiadmins) {
                //                 echo "there are multiple";
                $adminrecipients = explode(',', $adminemails);
            } else {
                //                 echo $adminemails;
                $adminrecipients = $adminemails;
            }
        }
        
        $svc = new MailService();
        // to, subject, body, html mode, cc
        $rc = $svc->sendMail($item->email, $subject, $body, true, $adminrecipients);
        if ($rc) {
            return true;
        } else {
            return false;
        }
        
    }
}

