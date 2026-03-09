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
namespace FP4P\Component\JSports\Site\Campaigns;

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Router\Route;
use FP4P\Component\JSports\Site\Services\CampaignService;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;

class PromoBarCampaign extends Campaign
{

    public $url = "";

    public $assetid = 0;

    public $content;

    public function __construct($data)
    {
        $this->loadObject($data);
    }

    /**
     *
     * @return string
     */
    private function getAssetUrl()
    {
        $asset = CampaignService::getAsset($this->sponsorid, $this->assetid);

        $url = SponsorHelper::getAssetURL($this->sponsorid, $asset->filename);
        return $url;
    }

    public function toHtml($position)
    {

        // $url = $this->getAssetUrl();

        // $redirect = (strlen($this->url) > 0) ? 1 : 0;
        $output = '';

        if (strlen($this->customcss) > 0) {
            $output .= "<style>" . $this->customcss . "</style>";
        }

        // $output .= "<div id='jsports-campaign-{$position}' class='jsports-campaign-slot jsports-campaign-img-content {$this->classname}'>";
        $output .= '';

        // if ($redirect) {
        $urlstring = "index.php?option=com_jsports&task=campaign.click&id={$this->id}";
        $clickurl = Route::_($urlstring);
        // $output .= "<a class='jsports-img-link' target='_blank' href='{$clickurl}' rel='noopener noreferrer'>";
        // }

        $html = <<<HTML
        
        
        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const bar = document.getElementById('promoBar');
            const closeBtn = bar.querySelector('.promo-bar__close');
        
            // Optional: remember dismissal
            const storageKey = 'promoBarDismissed';
            if (localStorage.getItem(storageKey) === '1') return;
        
            // Open after load (smooth slide)
            setTimeout(() => bar.classList.add('is-open'), 350);
        
            // Close
            closeBtn.addEventListener('click', () => {
              bar.classList.remove('is-open');
              localStorage.setItem(storageKey, '1');
            });
          });
        </script>
        
        <div id="promoBar" class="promo-bar promo-bar--dark" role="region" aria-label="Announcement">
          <div class="promo-bar__inner">
            <div class="promo-bar__text">
                <strong>Sponsor Promo:</strong>
                {$this->content}
                <a href="/sponsors">Learn more</a>
            </div>
        
            <button type="button" class="promo-bar__close" aria-label="Close announcement">&times;</button>
          </div>
        </div>
        
        
        HTML;

        $output .= $html;
        //$output .= "</div>";

//         echo "<pre>" . $output . "</pre>";
//         exit;
        return $output;
    }
}

