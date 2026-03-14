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

namespace FP4P\Component\JSports\Site\Helpers;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;

final class DateHelper
{

    /**
     * Return the current Joomla site time
     */
    public static function now(): Date
    {
        return Factory::getDate();
    }
    
    /**
     * Return formatted site time
     */
    public static function nowFormatted(string $format = 'Y-m-d g:i A'): string
    {
        $tz = new \DateTimeZone(Factory::getApplication()->get('offset', 'UTC'));
        
        return (new \DateTimeImmutable('now', $tz))->format($format);
        
//         return Factory::getDate()->format($format, true);
    }
    
    /**
     * Convert database UTC date to site timezone
     */
    public static function fromDatabase(string $date, string $format = 'Y-m-d g:i A'): string
    {
        $d = Factory::getDate($date);
        
        return $d->format($format, true);
    }
    
    /**
     * Return UTC date for database storage
     */
    public static function toDatabase(): string
    {
        return Factory::getDate()->toSql();
    }
}

