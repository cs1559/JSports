<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Ads;

use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use Joomla\CMS\Layout\LayoutHelper;

class SponsorRenderer implements RendererInterface
{

    public function render(Ad $ad, $position = null)
    {
        $sponsor = $ad;
        
        // Set basepath of the layouts as this class COULD be called from a module.
        // NOTE:  if this class is only called from within the component, the basePath would NOT be required.
        $basePath = JPATH_SITE . '/components/com_jsports/layouts';
        
        // set local variables
        $logo = SponsorHelper::getLogoURL($sponsor->id, $sponsor->logo);
        $website = $sponsor->website;
        //$sponsorship = Sponsorservice::getActiveSponsorship($sponsor->id);
        $sponsorshipid = $sponsor->sponsorshipid;
    
        
        $clickurl = $ad->getClickUrl();
        
        $data = [
            'position' => $position,
            'logo' => $logo,
            'sponsor' => $sponsor,
            'website' => $website,
            'clickurl' => $clickurl,
        ];
        
        echo LayoutHelper::render('jsports.ads.sponsors', $data, $basePath);

    }
}

