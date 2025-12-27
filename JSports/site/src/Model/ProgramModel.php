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
 * Methods supporting a list of mywalks records.
 *
 * @since  1.6
 */
class ProgramModel extends FormModel
{
    
    /**
     * @var     object  The user profile data.
     * @since   1.6
     */
    protected $data;
    
    
    protected $form = 'program';
    
    
    public function getData(){
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new RegistrationService();
        return $svc->getItem($id);
        
    }
    
    
    public function getItem(){
        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new RegistrationService();
        return $svc->getItem($id);

       
    }
    
    
    public function getForm($data = array(), $loadData = true)
    {
        
//         $form = $this->loadForm('com_jsports.registration', 'registration', ['control' => 'jform', 'load_data' => true]);
        
        $form = $this->loadForm(
            'com_jsports_form.registration.data', // just a unique name to identify the form
            'registration',				     // the filename of the XML form definition
            // Joomla will look in the models/forms folder for this file
            array(
                'control' => 'jform',	// the name of the array for the POST parameters
                'load_data' => $loadData	// will be TRUE
            )
            );
                
        if (empty($form))
        {
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }
        
        return $form;
    }
    
    protected function loadFormData()
    {
        
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_jsports_form.registration.data',	// a unique name to identify the data in the session
//             array("name" => "Coach Name")
                array($this->data)	// prefill data if no data found in session
            );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        $this->preprocessData('jsports.registration', $data);
        
        
        return $data;
    }
    
}