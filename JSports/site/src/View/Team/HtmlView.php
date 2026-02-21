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
 * HTML Team Profile View
 *
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
        /** @var \FP4P\Component\JSports\Site\Model\TeamsModel $model */  
        $model = $this->getModel();
        
        $this->data                     = $model->getData();
        $this->state                    = $model->getState();
        $this->item                     = $model->getItem();
        $this->standings                = $model->standings;
        $this->programs                 = $model->programs;
        $this->programstats             = $model->recordhistory;
        $this->recentprogram            = $model->recentprogram;
        $this->rosterstaff              = $model->rosterstaff;
        $this->rosterplayers            = $model->rosterplayers;
        $this->canSeeRoster             = $model->canSeeRoster;
        $this->games                    = $model->games;
        $this->canEditTournamentFlag    = $model->canEditTournamentFlag;
        $this->canEditAttributes        = $model->canEditAttributes;
        $this->programid                = $model->recentprogram->id;
        $this->form                     = $this->getModel()->getForm($this->item,true);
        $this->teamid                   = $this->data->id;
        
        $this->form->bind($this->item);
        
        $context = array('teamid' => $this->data->id,
            'ownerid' => $this->data->ownerid,
            'programid' => $model->recentprogram->id
        );
        /** Check to see if the current user can EDIT the team profile */
        $this->canEdit = SecurityService::canEditTeam($context);

        /** Increment the HIT counter ONLY if the user cannot EDIT the profile */
        if (!$this->canEdit) {
            TeamService::hit($this->data->id);
        }
        
        $this->active = TeamService::isActive($this->data->id);
        
        // Retrieve the directory for this teams logo
        $params = ComponentHelper::getParams('com_jsports');
        $logodir = $params->get('logodir');
        $this->attributesenabled = $params->get('enableattributes');
        
        if (isset($this->standings[0])) {
            $this->divisionname = $this->standings[0]['divisionname'];
        } else {
            $this->divisionname = "Not Available";
        }
        
        /** Get the PROFILE MENU if the current user can edit the profile */
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

        /** 
         * If the user is NOT a coach do not show the teams contact information - unless the team explicitly set the 
         * show contact info attribute on the profile.
         */
        if (!SecurityService::isCoach() && !$this->data->showcontactinfo) {
            $this->data->contactphone = 'Unavailable';
            $this->data->contactemail = 'Unavailable';
        }
        
         // Check for errors.
        if (count($errors = $model->getErrors()))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }    
  
        $document = Factory::getApplication()->getDocument();
        $wa = $this->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_jsports');
        $wa->useScript('com_jsports.phone-formatter.script');
        $phoneSelector = '#jform_contactphone';
        $document->addScriptOptions('com_jsports.phone', [
            'selector' => $phoneSelector
        ]);
        
        return parent::display($tpl);
    }
}

