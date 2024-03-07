<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     0.0.1
 * @package     JSports
 * @subpackage  Games.Site
 * @category    Model
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\FormModel;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Services\TeamService;

/**
 * GameModel - Methods/functions to manage games within the component.
 *
 * @since  1.6
 */
class GameModel extends FormModel
{
    
    /**
     * @var     object  The game data.
     * @since   1.6
     */
    protected $data;
    
    protected $form = 'game';
    
    protected $teamid;
    protected $programid;
    protected $divisionid;
    

    
    
    public function getItem(){

        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
     
        $svc = new GameService();
        $item = $svc->getItem($id);

       
        if ($id == 0) {
            $this->teamid = $input->get('teamid',0);
            $this->programid = $input->get('programid',0);
            $this->divisionid = TeamService::getTeamDivision($this->teamid, $this->programid);
        } 


        return $item;

    }
        
    
    public function getForm($data = array(), $loadData = true)
    {
        
        echo "GameModel::getForm";
        exit;
        
        $form = $this->loadForm('com_jsports.game', 'game', ['control' => 'jform', 'load_data' => true]);
        
        $form = $this->loadForm(
            'com_jsports_form.game.data', // just a unique name to identify the form
            'game',				     // the filename of the XML form definition
            array(
                'control' => 'jform',	// the name of the array for the POST parameters
                'load_data' => $loadData	// will be TRUE
            )
            );
                
        if (empty($form))
        {
            $errors = $this->getErrors();
            throw new Exception(implode("\n", $errors), 500);
        }
        
        return $form;
    }
    
    protected function loadFormData()
    {
        
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_jsports_form.game.data',	             // a unique name to identify the data in the session
                array($this->data)	                     // prefill data if no data found in session
            );
        
        if (empty($data)) {
            $data = $this->getItem();
            echo "GameModel::loadFormData";
            var_dump($data);
            exit;
        }        
        
        $this->preprocessData('jsports.game', $data);
        
        
        return $data;
    }
    
    
    /**
     * This function will save/store the data captured on the Registration EDIT form and save it to the database.
     *
     * @param unknown $data
     * @return unknown
     */
    public function save($data) {
        
        $user = Factory::getUser();
            
        $table = GameService::getGamesTable();
    	$table->bind($data);
    
    	// Set default values if its a new record.
    	if ($data['id'] == 0) {
            $table->enteredby = $user->username;	    
    	}
    	$table->updatedby = $user->username;   
    	$datetime = date_create()->format('Y-m-d H:i:s');
    	$table->dateupdated = $datetime;
    	
    	// Get Team Names
    	$hometeam = TeamService::getItem($data['hometeamid']);
    	$awayteam = TeamService::getItem($data['awayteamid']);
    	$table->hometeamname = $hometeam->name;
    	$table->awayteamname = $awayteam->name;
    	
    	$table->name = $awayteam->name . " @ " . $hometeam->name;
    	
    	$table->check();
	
	
    // 	// Block of code to prevent an "incorrect integer value" own the ownerid field.
    // 	if (strlen($data["ownerid"]) < 1) {	
    // 		$data["ownerid"] = 0;
    // 	}
            
            //@TODO Need to add code to catch any error that may exist.
    	if ($table->save($data)) {
    		return true;
    	} else {
    	    $errors = $table   ->getErrors();
    	    $this->setError($errors[0]);
    		$app = Factory::getApplication();
    		$app->enqueueMessage($errors[0],'error');
    		return false;
    	}

    	return true;        
    }
    
    

    
    
}