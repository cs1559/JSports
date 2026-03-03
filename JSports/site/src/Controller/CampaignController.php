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
        
        $campaign = CampaignService::getItem($id);
        // update click count
        CampaignService::click($id);
                
        $url = trim((string) $campaign->url);
        
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
