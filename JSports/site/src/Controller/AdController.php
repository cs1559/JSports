<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
use FP4P\Component\JSports\Site\Campaigns\CampaignHelper;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Ads\AdsManager;
use FP4P\Component\JSports\Site\Ads\Campaign;


/**
 * Site controller for ad campaign interactions (click tracking and redirect).
 *
 * @since  1.0.0
 */
class AdController extends BaseController
{

    /**
     * Handles an ad click: validates the HMAC signature, records the click if
     * it looks legitimate, and redirects the browser to the campaign's target URL.
     *
     * Expects the following request parameters:
     *  - id  (int)     Campaign ID.
     *  - ts  (int)     Timestamp the link was generated at.
     *  - sig (string)  HMAC-SHA256 signature of "{id}|{ts}" using the component's secret key.
     *
     * A click is only counted when the signature is valid, the link is no more
     * than an hour old, and the request doesn't look like a bot/crawler.
     *
     * @return  void
     *
     * @throws  \RuntimeException  If the campaign id is missing/invalid, or the
     *                             resolved redirect URL is not a valid absolute
     *                             http(s) URL.
     * @since   1.4.6
     */
    
    public function click() : void {

        $app   = Factory::getApplication();
//         $input = $app->input;
        $input = $app->getInput();
//         $db    = Factory::getContainer()->get(DatabaseInterface::class);
        
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
               
        $row = CampaignService::getItem($id);
        
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
        
        
    }

    /**
     * Debug/preview helper that renders a single ad slot directly to output.
     *
     * @return  void
     *
     * @since   1.4.6
     */
    public function test() : void {
                
        echo AdsManager::renderCampaign('standings-top');
        
    }

}
