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
use Joomla\CMS\CMSObject;
use Joomla\CMS\Component\ComponentHelper;
use FP4P\Component\JSports\Administrator\Helpers\Html;
use FP4P\Component\JSports\Administrator\Services\SecurityService;

/**
 * HTML Team View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{

    /**
     * The item object details
     *
     * @var    \JObject
     * @since  1.6
     */
    protected $item;
      
    protected $programs;
    protected $recentprogram;
    protected $rosterplayers;
    protected $rosterstaff;
    protected $canEdit = false;
    protected $standings;
    protected $canSeeRoster;
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        $this->data       = $this->get('Data');
        $this->state      = $this->get('State');
        $this->item       = $this->get('Item');

        $this->canEdit = SecurityService::canEditTeam($this->data->id,$this->data->ownerid);
        
        $this->form = $this->getModel()->getForm($this->item,true);
        $this->form->bind($this->item);

        $mod = $this->getModel();
        
        // Retrieve the directory for this teams logo
        $params = ComponentHelper::getParams('com_jsports');
        $logodir = $params->get('logodir');
        
        $this->programs = $mod->programs;
        $this->programstats = $mod->recordhistory;
        $this->recentprogram = $mod->recentprogram;
        $this->rosterstaff = $mod->rosterstaff;
        $this->rosterplayers = $mod->rosterplayers;
        $this->canSeeRoster = $mod->canSeeRoster;
        $this->games = $mod->games;
        
        
        if ($this->canEdit) {
            $this->profilemenu = Html::getTeamProfileMenu($this->data->id, "");
        }
        
        $defaultlogo = '/media/com_jsports/images/swibl-square-logo.png';
        if (strlen($this->data->logo) > 0) {
            $this->teamlogo = $logodir . "/Teamid-" . $this->data->id . "/" . $this->data->logo;
            if (!file_exists(JPATH_SITE . $this->teamlogo)) {
                $this->teamlogo = $defaultlogo;
            }
        } else {
            $this->teamlogo = $defaultlogo;
        }

        if (!SecurityService::isCoach()) {
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

