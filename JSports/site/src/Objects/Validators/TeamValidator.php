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
namespace FP4P\Component\JSports\Site\Objects\Validators;
use FP4P\Component\JSports\Site\Services\TeamService;
use FP4P\Component\JSports\Site\Objects\Validators\EmailValidator;
use FP4P\Component\JSports\Site\Objects\Validators\ValidatorResponse;

use Joomla\CMS\Factory;

class TeamValidator
{
    
    public $msg = "";
    
    public function validate($id) {
        
        $team = TeamService::getItem($id);
        $evalidator = new EmailValidator();
        
        $response = $evalidator->validate($team->contactemail);
        $this->msg = $response->getMsg();
        return $response->isValid();
    }
    
}

