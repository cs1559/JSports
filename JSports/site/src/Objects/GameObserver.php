<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\Objects;

use FP4P\Component\JSports\Site\Objects\BaseObserver;

class GameObserver extends BaseObserver
{
    
    static public function afterPostScore($args) {
        
//         if (!$args instanceof JLGame  ) {
//             return;
//         }
//         if ($args->isLeagueGame()) {
//             require_once(JLEAGUE_SERVICES_PATH .DS . 'teamservice.class.php');
//             $teamsvc = & JLTeamService::getInstance();
//             $hometeam = $teamsvc->getRow($args->getHometeamId());
//             $awayteam = $teamsvc->getRow($args->getAwayteamId());
            
//             $homeemails = $teamsvc->getTeamEmailAddresses($hometeam->getId());
//             $awayemails = $teamsvc->getTeamEmailAddresses($awayteam->getId());
//             $emails = array_merge($homeemails, $awayemails);
            
//             $config = &JLConfig::getInstance();
            
//             $fromemail = $config->getPropertyValue('email_from_addr');
//             $fromname = $config->getPropertyValue('email_from_name');
//             $emailtmpl = new JLTemplate("gamescorenotification");
//             $emailtmpl->setObject('game',$args);
//             $emailmsg = $emailtmpl->getContent();
            
//             //echo $emailmsg;
            
//             JLUtil::sendMail($fromemail,$fromname,$emails, "SWIBL - Game Score Posted", $emailmsg, true,null,"chris@swibl-baseball.org");
            
//         }

        return true;
    }
        
}

