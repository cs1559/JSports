<?php
namespace FP4P\Component\JSports\Site\Ads;

use Joomla\CMS\Layout\LayoutHelper;

class CampaignRenderer implements RendererInterface
{

    public function render(Ad $ad, $position = null)
    {

        $campaign = $ad;
        
        // Set basepath of the layouts as this class COULD be called from a module.
        // NOTE:  if this class is only called from within the component, the basePath would NOT be required.
        $basePath = JPATH_SITE . '/components/com_jsports/layouts';
        
        $layout = $ad->getLayout();
        $clickurl = $ad->getClickUrl();
        
        $data = [
            'position' => $position,
            'campaign' => $campaign,
            'clickurl' => $clickurl,
        ];
        
        echo '<div class="jsports-ad" data-adid="' . $campaign->id . '" data-type="campaign">';
        $fqn = "jsports.ads.campaigns.{$layout}";
        echo LayoutHelper::render($fqn, $data, $basePath);
        echo '</div>';
        
        
    }
}

