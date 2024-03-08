<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */


namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\RegistrationService;

/**
 * This model supports methods required to enable users to register for a program from the front-end side of the JSports component.
 *
 * @since  1.6
 */
class RegistrationModel extends FormModel
{
    
    /**
     * @var     object  Registration data.
     * @since   1.6
     */
    protected $data;
    
    protected $agreementurl = null;
    
    
    /**
     * This function will retrieve a registration record from the database.
     * 
     * @return RegistrationTable
     */
    public function getData(){
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new RegistrationService();
        $item = $svc->getItem($id);
    
        $this->agreementurl = "https://cdn1.sportngin.com/attachments/document/446a-3026828/swibl_coaching_agreement.pdf";
        
        return $item;
    }

    /**
     * This function will retrieve a registration record from the database.
     *
     * @return RegistrationTable
     */
   
    public function getItem(){
        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new RegistrationService();
        $item = $svc->getItem($id);

        return $item;
    }
    
    
    /**
     * This function will return an instance of the form associated with the Registration Model.  
     * 
     * @param array $data
     * @param boolean $loadData
     * @throws Exception
     * @return unknown
     */
    public function getForm($data = array(), $loadData = true)
    {
        
        $form = $this->loadForm('com_jsports.registration', 'registration', ['control' => 'jform', 'load_data' => true]);
        
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
            throw new Exception(implode("\n", $errors), 500);
        }
        
        return $form;
    }
    
    /**
     * This function will load data into the form associated with the model.
     * @return unknown
     */
    protected function loadFormData()
    {
        
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_jsports_form.registration.data',	// a unique name to identify the data in the session
                array($this->data)	// prefill data if no data found in session
            );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        $this->preprocessData('jsports.registration', $data);
        
        
        return $data;
    }
    
    
    /**
     * This function will save/store the data captured on the Registration EDIT form and save it to the database.
     * 
     * @param unknown $data
     * @return unknown
     */
    public function save($data) {
        
        $reg = RegistrationService::getRegistrationTable();
        
        // Set default values for certain fields
        $reg->ipaddr = $_SERVER['REMOTE_ADDR']; 
        $reg->regdate = date("Y-m-d H:i:s");

        //@TODO Need to add code to catch any error that may exist.
        return $reg->save($data);
        
    }
}