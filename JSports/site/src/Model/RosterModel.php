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
use FP4P\Component\JSports\Site\Services\RosterService;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;

/**
 * Methods supporting a list of mywalks records.
 *
 * @since  1.6
 */
class RosterModel extends FormModel
{
    
    /**
     * @var     object  The user profile data.
     * @since   1.6
     */
    protected $data;
    
    protected $programs;
    protected $recordhistory;
    
    protected $form = 'roster';
       
    
    public function getItem(){

        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new RosterService();
        $item = $svc->getItem($id);
        
        if ($id == 0) {
            $item->teamid = $input->get('teamid',0);
            $item->programid = $input->get('programid',0);
        }

        return $item;

    }
        
    
    public function getForm($data = array(), $loadData = true)
    {
        
//         $form = $this->loadForm('com_jsports.roster', 'roster', ['control' => 'jform', 'load_data' => true]);
        
        $form = $this->loadForm(
            'com_jsports_form.roster.data', // just a unique name to identify the form
            'roster',				     // the filename of the XML form definition
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
            'com_jsports_form.roster.data',	// a unique name to identify the data in the session
                array($this->data)	// prefill data if no data found in session
            );
        
        if (empty($data)) {
            $data = $this->getItem();
        }
        
        $this->preprocessData('jsports.roster', $data);
        
        
        return $data;
    }
    
    
    /**
     * This function will save/store the data captured on the Registration EDIT form and save it to the database.
     *
     * @param array $data
     * @return boolean
     */
    public function save($data) {
        
        $logger = Myapp::getLogger();
        $roster = RosterService::getRostersTable();
    
    	$roster->bind($data);
    	$roster->check();
    
            
            //@TODO Need to add code to catch any error that may exist.
    	if ($roster->save($data)) {
    	    if ($roster->staffadmin) {
    	        $adminFlag = 'Yes';
    	    } else {
    	        $adminFlag = 'No';
    	    }
    	    $logger->info('Saving roster item id - ' . $roster->id . ' Name: '. $roster->firstname . ' ' . 
    	           $roster->lastname . ' ADMIN='. $adminFlag . ' TYPE=' . $roster->classification);
    		return true;
    	} else {
    	    $errors = $roster->getErrors();
    	    $this->setError($errors[0]);
    		$app = Factory::getApplication();
    		$app->enqueueMessage($errors[0],'error');
    		return false;
    	}
    
        	return true;
    }
    
    

    
    
}