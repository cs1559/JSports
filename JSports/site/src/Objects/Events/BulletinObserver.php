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

class BulletinObserver extends BaseObserver
{
    
    public static function onAfterBulletinSave($args) {
        
        $params =       ComponentHelper::getParams('com_jsports');
        $data           = (object) $args['data'];
        $adminemails    = $params->get('adminemail');
        $multiadmins    = strpos(',', $adminemails);
        $orgemail       = $params->get('orgemail');
        
        $sendemails     = $params->get('bulletinapprovalemail');
        
        if (!$sendemails) {
            return true;
        }
        
        $body = "
<p>A new bulletin has been posted/saved and needs to be approved by the league. </p>
<p>
<strong>NOTE:  All bulletins that have been updated will no longer be visibile on the website UNTIL it has been approved.</strong>
</p>
<p>
<strong>Title: </strong> " . $data->title . "<br/>
<strong>Content: </strong><br/>" . $data->content . "
<br/>
<strong>Updated by:</strong>: " . $data->updatedby . "<br/>
</p>
<p>
SWIBL<br/>
Email: " . $orgemail . "<br/>
</p> ";
        
        $subject = "SWIBL - Bulletin Approval Required";
 
        $adminrecipients = array();
        if ($multiadmins) {
            $adminrecipients = explode(',', $adminemails);
        } else {
            $adminrecipients = $adminemails;
        }
        
        $svc = new MailService();
        // to, subject, body, html mode, cc
        return $svc->sendMail($adminrecipients, $subject, $body, true );

    }
    
}

