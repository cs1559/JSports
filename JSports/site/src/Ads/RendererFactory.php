<?php
namespace FP4P\Component\JSports\Site\Ads;

class RendererFactory
{
    public static function create(Ad $ad): RendererInterface
    {
        $renderer = match ($ad->type) {
            'Sponsor'  => new SponsorRenderer(),
            'Campaign' => new CampaignRenderer(),
            default    => throw new \RuntimeException('Unknown ad type')
        };
        
//         $renderer = new ImpressionWrapperDecorator($renderer);
        
//         if (!empty($ad->link)) {
//             $renderer = new LinkDecorator($renderer);
//         }
        
//         if (!empty($ad->trackClicks)) {
//             $renderer = new TrackingDecorator($renderer);
//         }
        
        return $renderer;
    }
}

