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

namespace FP4P\Component\JSports\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use FP4P\Component\JSports\Site\Objects\Standings\StandingsEngine;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\MailService;
use FP4P\Component\JSports\Site\Services\CampaignService;
use FP4P\Component\JSports\Site\Ads\Campaign;
use FP4P\Component\JSports\Site\Campaigns\CampaignHelper;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Helpers\JSHelper;


/**
 */
class CampaignController extends BaseController
{

    /**
     * @param boolean $cachable
     * @param array $urlparams
     */   
    public function click() {

        $app   = Factory::getApplication();
        $input = $app->input;
        $db    = Factory::getContainer()->get(DatabaseInterface::class);
        
        // 1) Campaign id only
        $id = $input->getInt('id', 0);
        if ($id <= 0) {
            throw new \RuntimeException('Invalid campaign id', 400);
        }
        
        $params = ComponentHelper::getParams('com_jsports');
        $secret = $params->get('secretkey', "jsports");
        
        $ts = $input->getInt('ts');
        $sig = $input->getString('sig', '');
        
        $expected = hash_hmac('sha256', $id . '|' . $ts, $secret);
        
        $isValid = hash_equals($expected, $sig);
        $isFresh = $ts > 0 && abs(time() - $ts) <= 3600;
        $isBot = JSHelper::isBot();
        
        $shouldCount = $isValid && $isFresh && !$isBot;
               
        $row = CampaignService::getCampaign($id);
        
        // Load a campaign object.
        $campaign = new Campaign();
        $campaign->loadObject($row);
        
        
        // update click count
        if ($shouldCount) {
            CampaignService::click($id);
        }
        $url = trim((string) $campaign->getRedirectUrl());
        
        // Allow only absolute http(s) URLs (recommended)
        $parts = Uri::getInstance($url)->toString() ? parse_url($url) : null;
        $scheme = $parts['scheme'] ?? '';
        if (!in_array(strtolower($scheme), ['http', 'https'], true)) {
            throw new \RuntimeException('Invalid destination URL', 400);
        }
        
        // 5) Redirect
        $app->redirect($url);
        
        
        //$this->setRedirect(Route::_('index.php?option=com_jsports&view=postscores&teamid=' . $redirectteamid, false));
        
    }
    

}
