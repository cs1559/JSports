<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
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
use Joomla\CMS\Filter\OutputFilter; 

class TeamsTable extends Table
{
    function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__jsports_teams', 'id', $db);

    }
    function check() {
        
        // Apply the FilterOutput stringURLUnicodeSlug function
        if (is_null($this->alias)) {
            $this->alias = OutputFilter::stringURLUnicodeSlug($this->name);
        } else {
            if (strlen($this->alias)<1) {
                $this->alias = OutputFilter::stringURLUnicodeSlug($this->name);
            }
        }
        return parent::check();
    }
}