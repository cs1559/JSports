<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseModel;

class ReportsModel extends BaseModel
{
    
    public function getSummary(): array
    {
        return [
            'teams'     => 159,
            'divisions' => 19,
        ];
    }

    public function getTableRows(): array
    {
        return [
            ['team' => 'Athletics', 'wins' => 5],
            ['team' => 'Sox', 'wins' => 3],
        ];
    }
}
