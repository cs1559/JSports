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
use Joomla\CMS\Language\Text;
use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Services\TeamService;
use Joomla\CMS\Application\SiteApplication;
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;
use Joomla\CMS\Form\Form;
use FP4P\Component\JSports\Site\Services\UserService;

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
    protected $team;
    protected $_context = 'com_jsports.game';
    protected $_item = [];
    protected $contextid = 0;
    
    protected function populateState() {
        
        parent::populateState();
        
        /** @var SiteApplication $app */
        $app = Factory::getContainer()->get(SiteApplication::class);
        $this->setState('game.id', $app->getInput()->getInt('id'));
        $this->setState('game.teamid', $app->getInput()->getInt('teamid'));
        $this->setState('game.programid', $app->getInput()->getInt('programid'));
        
    }
    
    /**
     * This function is an override function of the FormModel validate function.  This is a
     * server side validation that adds a check to see if the teamid is equal to the opponent ID.
     * If that passes validation, then the parent validate function is called.  Otherwise, a false boolean
     * is returned.
     *
     * @param Form $form
     * @param array $data
     * @param string $group
     * @return array | boolean
     */
    public function validate($form, $data, $group = null) {
        
        if ($data['teamid'] == $data['opponentid']) {
            $this->setError(Text::_('COM_JSPORTS_ERR_OPPONENT_SAME'));
            return false;
        }
        
        if ($data['leaguegame']) {
            if (!$data['opponentid']) {
                $this->setError('Missing opponent');
                return false;
            }
        }
        return parent::validate($form, $data, $group);
    }
    
    
    
    public function getItem($pk = null){
        
        $input = Factory::getApplication()->input;
        $id = $pk ?? $input->getInt('id');
        $this->contextid = $input->getInt("contextid");
        
        $svc = new GameService();
        $item = $svc->getItem($id);
        if (!$item) {
            $this->setError(Text::_('COM_JSPORTS_ERR_MISSING_GAME'));
            return null;
        }
        
        if ($id == 0) {
            $this->teamid = $input->getInt('teamid');
            $this->programid = $input->getInt('programid');
            $this->divisionid = TeamService::getTeamDivisionId($this->teamid, $this->programid);
        }
        
        // Get a Team record and place within our model
        $this->team = TeamService::getItem($item->teamid);

        if (is_null($this->team)) {
            $this->setError(Text::_('COM_JSPORTS_ERR_MISSING_TEAM'));
        } elseif ($this->contextid < 1) {
            $this->contextid = $this->team->id;
        }
        return $item;
    }
    
    
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_jsports.game', 'game', ['control' => 'jform', 'load_data' => true]);
        
        if (empty($form))
        {
            return false;
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }
        $game = $this->getItem($this->getState('game.id'));
//         $game = $this->getItem();
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
//             echo "GameModel::loadFormData";
//             var_dump($data);
//             exit;
        }
        
        $this->preprocessData('com_jsports.game', $data);
        
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
        $isNew = false;
        
//         $user = Factory::getUser();
        $user = UserService::getUser();
        
        $table = GameService::getGamesTable();
        $table->bind($data);
        
        // Set default values if its a new record.
        if ($data['id'] == 0) {
            $table->enteredby = $user->username;
            $isNew = true;
        }
        $table->updatedby = $user->username;
        $datetime = date_create()->format('Y-m-d H:i:s');
        $table->dateupdated = $datetime;
        
        if ($data['leaguegame']) {
            if ($data['homeindicator']) {
                $hometeam = TeamService::getItem($data['teamid']);
                $awayteam = TeamService::getItem($data['opponentid']);
                $table->hometeamid = $data['teamid'];
                $table->hometeamname = $hometeam->name;
                $table->awayteamid = $data['opponentid'];
                $table->awayteamname = $awayteam->name;
            } else {
                $hometeam = TeamService::getItem($data['opponentid']);
                $awayteam = TeamService::getItem($data['teamid']);
                $table->hometeamid = $hometeam->id;
                $table->hometeamname = $hometeam->name;
                $table->awayteamid = $awayteam->id;
                $table->awayteamname = $awayteam->name;
            }
            $table->name = $awayteam->name . " @ " . $hometeam->name;
        } else {
            if ($data['homeindicator']) {
                $hometeam = TeamService::getItem($data['teamid']);
                $table->hometeamid = $data['teamid'];
                $table->hometeamname = $hometeam->name;
                $table->awayteamid = 0;
                $table->awayteamname = $data['nonleagueteam'];
            } else {
                $awayteam = TeamService::getItem($data['teamid']);
                $table->hometeamid = 0;
                $table->hometeamname = $data['nonleagueteam'];
                $table->awayteamid = $awayteam->id;
                $table->awayteamname = $awayteam->name;
            }
            $table->name = $table->awayteamname . " @ " . $table->hometeamname;
        }
        
        //$table->name = $awayteam->name . " @ " . $hometeam->name;
        
        $table->check();
        
        //@TODO Need to add code to catch any error that may exist.
        if ($table->save($data)) {
            if ($isNew) {
                $logger->info('Game id: ' . $table->id . " - " .
                    $table->name . " has been inserted - Date: " . $table->gamedate . " Time: " . JSHelper::displayGameTime($table->gametime));
            } else {
                $logger->info('Game id: ' . $table->id . ' has been updated - Date: ' .
                    $table->gamedate . ' Time: ' . JSHelper::displayGameTime($table->gametime) . ' STATUS = ' . $data['gamestatus']);
            }
            return true;
        } else {
            $errors = $table->getErrors();
            $this->setError($errors[0]);
            $app = Factory::getApplication();
            $app->enqueueMessage($errors[0],'error');
            return false;
        }
        
        return true;
    }
    
    
}