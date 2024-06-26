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
        
        // @TODO - This needs to be refactored where the email content is abstracted from this class into more of a
        //      template solutions.
        $body = "
<p>THANK YOU for registering for </p>
<p>
<p>
SWIBL<br/>
Email: info@swibl.org<br/>
</p> ";
        
        $subject = "SWIBL - New Registration";
        
        $mainemails = "cs1559@localhost";
        $swiblemails = "chris@swibl.org";
        $recipients = array_merge($homeemails, $awayemails);
        
        $adminrecipients = array();
        if ($ccadmin) {
            if ($multiadmins) {
                $adminrecipients = explode(',', $adminemails);
            } else {
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
    
}

