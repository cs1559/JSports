<?php
namespace FP4P\Component\JSports\Site\Campaigns;

class CampaignFactory
{
    public static function createCampaign(object $data) {
        
        $campaign = match ($data->campaigntype) {
            'T' => new Textcampaign($data),
            'I' => new Imagecampaign($data),
            'D' => new TextWithAssetCampaign($data),
            'P' => new PromoBarCampaign($data),
        };

        return $campaign;
    }
}

