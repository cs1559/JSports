<?php
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Campaigns Controller
 *
 */
class CampaignsController extends AdminController
{
    protected $default_view = 'campaigns';
    
    public function getModel(
        $name = 'Campaign',
        $prefix = 'Administrator',
        $config = ['ignore_request' => true]
        ) {
            return parent::getModel($name, $prefix, $config);
    }
    
    public function publish() {
        $model = $this->getModel('Campaign');
        
        parent::publish();
    }
}