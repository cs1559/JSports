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

namespace FP4P\Component\JSports\Site\Objects\Reports;

use InvalidArgumentException;

class ReportFactory {
    
    public static function create(string $type): Report {
        return match ($type) {
            'noroster' => new NoRosterReport(),
            'registrationlist' => new Registrationlist(),
            default => throw new InvalidArgumentException("Unknown type."),
        };
    }
}

