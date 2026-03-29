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