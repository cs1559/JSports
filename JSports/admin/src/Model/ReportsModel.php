<?php

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
