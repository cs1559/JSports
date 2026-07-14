<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

namespace FP4P\Component\JSports\Site\Reports;

use InvalidArgumentException;

class ReportFactory {
    
    public static function create(string $type): Report {
        return match ($type) {
            'noroster' => new NoRosterReport(),
            'registrationlist' => new Registrationlist(),
            'duplicateplayers' => new Duplicateplayers(),
            'rundifferential' => new RunDifferential(),
            'sponsorshipclick' => new SponsorshipclickReport(),
            'campaignsclick' => new CampaignsclickReport(),
            'gamescores' => new Gamescores(),
            default => throw new InvalidArgumentException("Unknown type."),
        };
    }
}

