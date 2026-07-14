<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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

