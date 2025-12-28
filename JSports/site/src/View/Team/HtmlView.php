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

namespace FP4P\Component\JSports\Site\View\Team;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Administrator\Helpers\Html;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;
use FP4P\Component\JSports\Administrator\Table\ProgramsTable;

/**
 * HTML Team View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    /**
     * 
     * @var TeamsTable
     */
    protected $item;  
    
    protected $programs;
    /**
     * 
     * @var ProgramsTable
     */
    protected $recentprogram;
    /**
     * This variable is an array of objects that correspond to the Rosters table for a specific classification type (P)
     * 
     * @var array
     */
    protected $rosterplayers;
    /**
     * This variable is an array of objects that correspond to the Rosters table for a specific classification type (S)
     *
     * @var array
     */ 
    protected $rosterstaff;
    /**
     * Flag to determine if the user can edit the team profile.
     * @var boolean
     */
    protected $canEdit = false;
    /**
     * This is an array of array of standings records.  The standingservice returns an associated list from the standings query.
     * @var array
     */
    protected $standings;
    /**
     * Flat to indicate if the user can see the team's roster
     * @var boolean
     */
    protected $canSeeRoster;
    /**
     * Division Name
     * @var string
     */
    protected $divisionname;
    /**
     * Flag to indicate if the team is active for the current season/program.
     * @var boolean
     */
    protected $active;
    /**
     * Team ID - required to define context for the request
     * @var int
     */
    protected $teamid;
    /**
     * Program ID - required to define context
     * @var int
     */
    protected $programid;
    /**
     * 
     * @var boolean
     */
    protected $canEditTournamentFlag = false;
    /**
     * Flag to indicate whether or not the team profile attribtues are enabled and editable by team admins.  This is set
     * at the component options level.
     * 
     * @var boolean
     */
    protected $attributesenabled = false;
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        // @TODO  This "getters" will need to be updated for Joomla 7
        $this->data       = $this->get('Data');
        $this->state      = $this->get('State');
        $this->item       = $this->get('Item');
       
        $this->form = $this->getModel()->getForm($this->item,true);
        $this->form->bind($this->item);
               
//    $user = Factory::getUser();
//         $user = Factory::getApplication()->getIdentity();

        $mod = $this->getModel();

        $this->teamid = $this->data->id;
        $this->programid = $mod->recentprogram->id;
        
        $context = array('teamid' => $this->data->id,
            'ownerid' => $this->data->ownerid,
            'programid' => $mod->recentprogram->id
        );
        $this->canEdit = SecurityService::canEditTeam($context);

        if (!$this->canEdit) {
            TeamService::hit($this->data->id);
        }
        
        $this->active = TeamService::isActive($this->data->id);
        $this->standings = $mod->standings;
        
        // Retrieve the directory for this teams logo
        $params = ComponentHelper::getParams('com_jsports');
        $logodir = $params->get('logodir');
        $this->attributesenabled = $params->get('enableattributes');
        
        $this->programs = $mod->programs;
        $this->programstats = $mod->recordhistory;
        $this->recentprogram = $mod->recentprogram;
        $this->rosterstaff = $mod->rosterstaff;
        $this->rosterplayers = $mod->rosterplayers;
        $this->canSeeRoster = $mod->canSeeRoster;
        $this->games = $mod->games;
        $this->canEditTournamentFlag = $mod->canEditTournamentFlag;
        $this->canEditAttributes = $mod->canEditAttributes;
        
        if (isset($this->standings[0])) {
            $this->divisionname = $this->standings[0]['divisionname'];
        } else {
            $this->divisionname = "Not Available";
        }
        
        if ($this->canEdit) {
            $this->profilemenu = Html::getTeamProfileMenu($this->data->id, "");
        }
        
        //@TODO  This default logo needs to be configurable
        $defaultlogo = '/media/com_jsports/images/swibl-square-logo.png';
        // Test for NULL to avoid deprecated error with strlen
        if (!is_null($this->data->logo)) {
            if (strlen($this->data->logo) > 0) {
                $this->teamlogo = $logodir . "/Teamid-" . $this->data->id . "/" . $this->data->logo;
                if (!file_exists(JPATH_SITE . $this->teamlogo)) {
                    $this->teamlogo = $defaultlogo;
                }
            } 
        } else {
            $this->teamlogo = $defaultlogo;
        }

        
        if (!SecurityService::isCoach() && !$this->data->showcontactinfo) {
            $this->data->contactphone = 'Unavailable';
            $this->data->contactemail = 'Unavailable';
        }
        
         // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
    }
}

