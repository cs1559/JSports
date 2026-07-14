<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace FP4P\Component\JSports\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\Database\ParameterType;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseModel;
use FP4P\Component\JSports\Site\Services\ProgramsService;


class CloseprogramModel extends BaseModel
{
   
    public function getItem(){
        
        $input = Factory::getApplication()->input;
        $id     = $input->getInt("id");
        
        $svc = new ProgramsService();
        return $svc->getItem($id);
        
        
    }
}

