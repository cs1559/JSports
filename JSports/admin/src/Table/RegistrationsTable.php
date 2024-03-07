<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     0.0.1
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class RegistrationsTable extends Table
{
    function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__jsports_registrations', 'id', $db);
//         $table->def('status','P');
//         $table->def('active',0);
//         $table->def('setupfinal',0);
//         return $table;
    }
}