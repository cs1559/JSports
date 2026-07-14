<?php   
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;
use FP4P\Component\JSports\Site\Objects\Application;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\RegistrationService;
use FP4P\Component\JSports\Administrator\Table\RegistrationsTable;
use Joomla\CMS\Form\Form;
use FP4P\Component\JSports\Site\Services\TeamService;

/**
 * This model supports methods required to enable users to register for a program from the front-end side of the JSports component.
 *
 */
class RegistrationModel extends FormModel
{
    
    /**
     * @var     RegistrationsTable  
     */
    protected $data;
    
    /**
     * @var string
     */
    protected $agreementurl = null;
    protected $registrationtemplate = 'default';
 
    
    protected function populateState()
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();
        
        $context = 'com_jsports.registration.data';
        
        
//         $programId = $input->getInt('programid', $app->getUserState($context . '.programid', 0));
//         $app->setUserState($context . '.programid', $programId);
        
//         $lastProgramId = $input->getInt('lastprogramid', $app->getUserState($context . '.lastprogramid', 0));
//         $app->setUserState($context . '.lastprogramid', $lastProgramId);
        
//         $returningTeam = $input->getInt('returningteam', $app->getUserState($context . '.returningteam', 0));
//         $app->setUserState($context . '.returningteam', $returningTeam);
        
//         $teamId = $input->getInt('teamid', $app->getUserState($context . '.teamid', 0));
//         $app->setUserState($context . '.teamid', $teamId);
        
//         $this->setState('program.id', $programId);
//         $this->setState('program.lastid', $lastProgramId);
//         $this->setState('team.returning', $returningTeam);
//         $this->setState('team.id', $teamId);
        
        parent::populateState();
    }
    
    
    /**
     * This function will retrieve a registration record from the database.
     * @deprecated
     * @return RegistrationsTable
     */ 
    public function getData() : RegistrationsTable {
//         $input = Factory::getApplication()->input;
//         $id     = $input->getInt("id");
        
//         $svc = new RegistrationService();
//         $item = $svc->getItem($id);
    
//         // @todo  This needs to be removed and make it a configurable option at the component level.
//         $this->agreementurl = "https://cdn1.sportngin.com/attachments/document/446a-3026828/swibl_coaching_agreement.pdf";
        
//         return $item;
    }

    /**
     * This function will retrieve a registration record from the database.
     *
     * @return RegistrationsTable
     */
   
    public function getItem(?int $id = null) : ?RegistrationsTable {
        
        $id = Factory::getApplication()->input->getInt('id', 0);
        
        $svc = new RegistrationService();
        return $svc->getItem($id);
        
    }
    
    
    /**
     * This function will return an instance of the form associated with the Registration Model.
     *
     * @param array $data
     * @param boolean $loadData
     * @throws \Exception
     * @return Form
     */
    public function getForm($data = array(), $loadData = true)
    {
        
//         $form = $this->loadForm('com_jsports.registration', 'registration', ['control' => 'jform', 'load_data' => true]);
        
        $form = $this->loadForm(
            'com_jsports_form.registration.data',   // just a unique name to identify the form
            'registration',				            // the filename of the XML form definition
            // Joomla will look in the models/forms folder for this file
            array(
                'control' => 'jform',	      // the name of the array for the POST parameters
                'load_data' => $loadData	  // will be TRUE
            )
         );
                
        if (empty($form))
        {
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }
        
        return $form;
    }
    
    /**
     * This function will load data into the form associated with the model.
     * @return array<string, mixed>
     */
    protected function loadFormData()
    {
        
        
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_jsports.registration.data', []);
        
        $team = TeamService::getItem($data['teamid']);
        $data['teamname'] = $team->name;
        $data['name'] = $team->contactname;
        $data['email'] = $team->contactemail;
        $data['phone'] = $team->contactphone;
        $data['city'] = $team->city;
        $data['state'] = $team->state;
        $data['teamid'] = $team->id;
        $data['existingteam'] = $data['returningteam'];
        
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        return $data;
        
//         // Check the session for previously entered form data.
//         $data = Factory::getApplication()->getUserState(
//             'com_jsports_form.registration.data',	// a unique name to identify the data in the session
//                 array($this->data)	// prefill data if no data found in session
//             );
        
//         if (empty($data)) {
//             $data = $this->getItem();
//         }
        
//         $this->preprocessData('jsports.registration', $data);
        
        return $data;
    }
    
    
    /**
     * This function will save/store the data captured on the Registration EDIT form and save it to the database.
     *
     * @param array<string, mixed> $data
     * @return boolean
     */
    public function save($data) {
        
        $app = Application::getInstance();
        
        
        $table = RegistrationService::getRegistrationTable();
        
        // Set default values for certain fields
        $table->ipaddr = $_SERVER['REMOTE_ADDR'];
        $table->regdate = date("Y-m-d H:i:s");

        //@TODO Need to add code to catch any error that may exist.
        if ($table->save($data)) {
            
//             $app->triggerEvent('onAfterRegistration', ['data' => $table]);
            
            //return true;
            return $table->id;
            
        } else {
            $errors = $table->getErrors();
            $this->setError($errors[0]);
            $app = Factory::getApplication();
            $app->enqueueMessage($errors[0],'error');
            return false;
        }
        
    }
}