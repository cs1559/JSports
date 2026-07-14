<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Campaigns;

class CampaignFactory
{
    public static function createCampaign(object $data) {
        
        $campaign = match ($data->campaigntype) {
            Textcampaign::TYPE => new Textcampaign($data),
            TextWithLinkCampaign::TYPE => new TextWithLinkCampaign($data),
            Imagecampaign::TYPE => new Imagecampaign($data),
            ImageWithTextCampaign::TYPE => new ImageWithTextCampaign($data),
            'P' => new PromoBarCampaign($data),
        };

        return $campaign;
    }
}

