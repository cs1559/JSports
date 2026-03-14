<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
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
            'gamescores' => new Gamescores(),
            default => throw new InvalidArgumentException("Unknown type."),
        };
    }
}

