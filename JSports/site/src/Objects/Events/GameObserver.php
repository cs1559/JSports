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
namespace FP4P\Component\JSports\Site\Objects\Events;

use FP4P\Component\JSports\Site\Objects\BaseObserver;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Services\MailService;
use Joomla\CMS\Component\ComponentHelper;

class GameObserver extends BaseObserver
{
    
    static public function onAfterPostScore($args) {
        
        $params = ComponentHelper::getParams('com_jsports');
        $data = (object) $args['data'];
        
        $notifyforfeit = $params->get('notifyforfeit');
        if ($notifyforfeit) {
            if (($data->hometeamscore == 7 && $data->awayteamscore == 0) || 
                ($data->hometeamscore == 0 && $data->awayteamscore == 7)) {
                GameObserver::sendForfeitNoticationEmail($args);
            }
        }
        
        $eventemails = $params->get('eventemails');
        if (!$eventemails) {
            return true;
        }
        
        $ccadmin = $params->get('ccadmin');
        $adminemails = $params->get('adminemail');
        $multiadmins = strpos(',', $adminemails);
        $orgemail = $params->get('orgemail');
        
        $data = (object) $args['data'];
        
        // Do not send an email for any NON-LEAGUE game.
        if (!$data->leaguegame) {
            return true;
        }
        
        // @TODO - This needs to be refactored where the email content is abstracted from this class into more of a
        //      template solutions.
        $body = "
<p>A league game score has been posted for a team you are associated with. The game score is listed below:</p>
<p>
<strong>ID: " . $data->id . " - " . $data->awayteamname . " @ " . $data->hometeamname . "</strong>
</p>
<table style=\"width: 40%\">
<tbody>
<tr><td>" . $data->hometeamname . "</td><td>" . $data->hometeamscore . "</td></tr>
<tr><td>" . $data->awayteamname . "</td><td>" . $data->awayteamscore . "</td></tr>
</tbody>
</table>
    
<br/>Please notify the league if there are any discrepancies with the score that has been posted.<br/>
<p>
SWIBL<br/>
Email: " . $orgemail . "<br/>
</p> ";
        
        $subject = "SWIBL - Game Score Posted";
        
        $homeemails = TeamService::getTeamEmailAddresses($data->hometeamid);
        $awayemails = TeamService::getTeamEmailAddresses($data->awayteamid);
        $recipients = array_merge($homeemails, $awayemails);
        
        $adminrecipients = array();
        if ($ccadmin) {
//             echo "email the admins";
//             echo $adminemails;
            if ($multiadmins) {
//                 echo "there are multiple";
                $adminrecipients = explode(',', $adminemails);
            } else {
//                 echo $adminemails;
                $adminrecipients = $adminemails;
            }
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
    
    
    private function sendGamescoreEmail($args) {
        $params = ComponentHelper::getParams('com_jsports');
        
        $ccadmin = $params->get('ccadmin');
        $adminemails = $params->get('adminemail');
        $multiadmins = strpos(',', $adminemails);
        $orgemail = $params->get('orgemail');-
        
        $data = (object) $args['data'];
        
        // Do not send an email for any NON-LEAGUE game.
        if (!$data->leaguegame) {
            return true;
        }
        
        // @TODO - This needs to be refactored where the email content is abstracted from this class into more of a
        //      template solutions.
        $body = "
<p>A league game score has been posted for a team you are associated with. The game score is listed below:</p>
<p>
<strong>ID: " . $data->id . " - " . $data->awayteamname . " @ " . $data->hometeamname . "</strong>
</p>
<table style=\"width: 40%\">
<tbody>
<tr><td>" . $data->hometeamname . "</td><td>" . $data->hometeamscore . "</td></tr>
<tr><td>" . $data->awayteamname . "</td><td>" . $data->awayteamscore . "</td></tr>
</tbody>
</table>
    
<br/>Please notify the league if there are any discrepancies with the score that has been posted.<br/>
<p>
SWIBL<br/>
Email: " . $orgemail . "<br/>
</p> ";
        
        $subject = "SWIBL - Game Score Posted";
        
        $homeemails = TeamService::getTeamEmailAddresses($data->hometeamid);
        $awayemails = TeamService::getTeamEmailAddresses($data->awayteamid);
        $recipients = array_merge($homeemails, $awayemails);
        
        $adminrecipients = array();
        if ($ccadmin) {
            //             echo "email the admins";
            //             echo $adminemails;
            if ($multiadmins) {
                //                 echo "there are multiple";
                $adminrecipients = explode(',', $adminemails);
            } else {
                //                 echo $adminemails;
                $adminrecipients = $adminemails;
            }
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
    
    public static function sendForfeitNoticationEmail($args) {
        $params = ComponentHelper::getParams('com_jsports');
        
        $ccadmin = $params->get('ccadmin');
        $adminemails = $params->get('adminemail');
        $multiadmins = strpos(',', $adminemails);
        $orgemail = $params->get('orgemail');
        
        $data = (object) $args['data'];
        
        // Do not send an email for any NON-LEAGUE game.
        if (!$data->leaguegame) {
            return true;
        }
        
        // @TODO - This needs to be refactored where the email content is abstracted from this class into more of a
        //      template solutions.
        $body = "
<p>A league game score has been posted that has been identified as a POSSIBLE forfiet score.  The game details are listed below:</p>
<p>
<strong>ID: " . $data->id . " - " . $data->awayteamname . " @ " . $data->hometeamname . "</strong>
</p>
<table style=\"width: 40%\">
<tbody>
<tr><td>" . $data->hometeamname . "</td><td>" . $data->hometeamscore . "</td></tr>
<tr><td>" . $data->awayteamname . "</td><td>" . $data->awayteamscore . "</td></tr>
</tbody>
</table>
    
<br/>Please notify the league if there are any discrepancies with the score that has been posted.<br/>
<p>
SWIBL<br/>
Email: " . $orgemail . "<br/>
</p> ";
        
        $subject = "SWIBL - Possible Forfiet Game Score Posted";
        
        $homeemails = TeamService::getTeamEmailAddresses($data->hometeamid);
        $awayemails = TeamService::getTeamEmailAddresses($data->awayteamid);
        $recipients = array_merge($homeemails, $awayemails);
        
        $adminrecipients = array();
//         if ($ccadmin) {
//             //             echo "email the admins";
//             //             echo $adminemails;
//             if ($multiadmins) {
//                 //                 echo "there are multiple";
//                 $adminrecipients = explode(',', $adminemails);
//             } else {
//                 //                 echo $adminemails;
                $adminrecipients = $adminemails;
//             }
//         }
        
        $svc = new MailService();
        // Recipients should be an array
        $rc = $svc->sendMail($adminrecipients, $subject, $body, true );
        if ($rc) {
            return true;
        } else {
            return false;
        }
        
    }
    
}

