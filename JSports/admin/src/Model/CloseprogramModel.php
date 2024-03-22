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

