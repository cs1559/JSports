<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Events;

use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Objects\BaseObserver;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\MailService;
use Joomla\CMS\Component\ComponentHelper;

class RegistrationObserver extends BaseObserver
{
    
    static public function onAfterRegistration($args) {
        
        $params = ComponentHelper::getParams('com_jsports');
        
        $eventemails = $params->get('eventemails');
        if (!$eventemails) {
            return true;
        }
        $ccadmin = $params->get('ccadmin');
        $adminemails = $params->get('adminemail');
        $multiadmins = strpos(',', $adminemails);
        
        $data = (object) $args['data'];
        $regid = $args['regid'];
        
        $program = ProgramsService::getItem($data->programid);
        
        $returningteam = "No";
        
        switch ($data->skilllevel) {
            case 'R':
                $skill = "Red"; 
                break;
            case 'W':
                $skill = "White";
                break;
            case 'B':
                $skill = "Blue";
                break;
            case 'E':
                $skill = "Elite";
                break;
            default:
                $skill = "";
                break;
        }
        
        if ($data->existingteam) {
            $returningteam = "Yes";
        }
        
        // @TODO - This needs to be refactored where the email content is abstracted from this class into more of a
        //      template solutions.
        $body = "
<p>Your registration has been received for " . $program->name . "</p>
<h2>REGISTRATION DETAILS</h2>
<p>
Registration ID: " . $regid . "<br/>
Team Name:  " . $data->teamname ."<br/>
Coach Name: " . $data->name . "<br/>
Email:  " . $data->email . "<br/>
Phone: " . $data->phone . "<br/>
Address: " . $data->address . "<br/>
City: " . $data->city . "<br/>
State: " . $data->state . "<br/>
Returning Team: " . $returningteam . " - " . "Id: " . $data->teamid . "<br/>
Playoffs: " . JSHelper::translateYesNo($data->playoffs) . "<br/>
</br>
Registration Group: " . $data->grouping . " - Skill Level:" . $skill ."<br/>
</br>
Registered By: " . $data->registeredby . "<br/>

</p>
<p>
SWIBL<br/>
Email: info@swibl.org<br/>
</p> ";
        
        $subject = "SWIBL - New " . $program->name . " Registration";
        
        $recipients = [$data->email];
        
        $adminrecipients = array();
        if ($ccadmin) {
            if ($multiadmins) {
                $adminrecipients = explode(',', $adminemails);
            } else {
                $adminrecipients = $adminemails;
            }
        }

        // Code to override email address for testing purposes.
        if (JSHelper::isTestServer()) {
            $recipients = ['cs1559@sbcglobal.net'];
            $body = "<h1>THIS IS ONLY A TEST</h1><br/>" . $body;
        }
        
        $svc = new MailService();
        // to, subject, body, html mode, cc
        $rc = $svc->sendMail($recipients, $subject, $body, true,$adminrecipients );
        if ($rc) {
            return true;
        } else {
            return false;
        }
    }
    
}

