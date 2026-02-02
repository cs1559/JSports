<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * NOTE:  CURRENTLY NOT USED
 */

namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\RegistrationService;

/**
 * Register Team Model used for registering an existing team for next 
 * season.
 *
 * THIS IS FUTURE
 *
 */
class RegisterteamModel extends FormModel
{
    
    /**
     * @var     object  The user profile data.
     * @since   1.6
     */
    protected $data;

    public function getData(){
        return null;
    }
    
 
    public function getItem(){
        return null;
    }
    
    
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            'com_jsports_form.register',  // just a unique name to identify the form
            'register',				// the filename of the XML form definition
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
        return Factory::getApplication()->getUserState(
            'com_jsports_form.register',	// a unique name to identify the data in the session
            array($this->data)	// prefill data if no data found in session
            );
        
//         return $data;
    }
    
}