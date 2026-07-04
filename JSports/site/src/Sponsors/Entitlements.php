<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace FP4P\Component\JSports\Site\Sponsors;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Services\CampaignService;
use Joomla\CMS\Router\Route;


class Entitlements
{
    /* @var array $source */
    public $source = array();
    
    /* @var number $maxcampaigns */
    public $maxcampaigns = 0;
    
    /* @var number $maxbulletins */
    public $maxbulletins = 0;
    
    /* @var string $positions */
    public $positions;
    
    /* @var number $usedcampaigns */
    public $usedcampaigns = 0;
    
    /* @var number usedbulletins */
    public $usedbulletins = 0;
    
    public function __construct() 
    {
    }
    
    private function loadJSON($entitlements) {
        $this->source[] = $entitlements;
    }
    
}

