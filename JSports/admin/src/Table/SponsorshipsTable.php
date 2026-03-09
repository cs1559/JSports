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
namespace FP4P\Component\JSports\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class SponsorshipsTable extends Table
{

        public $id = 0;
        public $sponsorid = 0;
        public $programid = 0;
        public $plancode = '';
        public $plantype = '';
        public $published = 1;
        
        public $startdate = null;  // <-- add
        public $enddate   = null;  // <-- add
        
        public function __construct(DatabaseDriver $db)
        {
            parent::__construct('#__jsports_sponsorships', 'id', $db);
        }
}