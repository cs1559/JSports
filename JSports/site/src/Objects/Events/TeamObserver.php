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
use FP4P\Component\JSports\Site\Objects\Application as Myapp;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\Factory;

/**
 * TeamObserver - This is an observer class that is used by the Event Dispatcher to support event notifications.
 * @author cs155
 *
 */
class TeamObserver extends BaseObserver
{
    
    static public function onAfterProfileOwnerUpdate($args) {
        
        extract($args);
        
        $params = ComponentHelper::getParams('com_jsports');
        $logger = Myapp::getLogger();
        
        $userFactory = Factory::getContainer()->get(UserFactoryInterface::class);
        if (intval($data['ownerid'])) {
            $user = $userFactory->loadUserById($data['ownerid']);
            $touser = $user->name;
        } else {
            $touser = "NOT DEFINED";
        }
        
        if (intval($origowner)) {
            $origuser = $userFactory->loadUserById($origowner);
            $fromuser = $origuser->name;
        } else {
            $fromuser = "NOT DEFINED";
        }
        
        $logger->info('TeamID: ' . $data['ownerid']. ' Team profile ownership has changed from ' . $fromuser . ' (UID:' . $origowner .
            ') to ' . $touser . '(UID:' . $data['ownerid'] . ')');
        
    }
    
}

