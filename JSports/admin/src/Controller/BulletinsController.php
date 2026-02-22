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

class BulletinsController extends AdminController
{
    protected $default_view = 'bulletins';
    
    public function getModel(
        $name = 'Bulletin',
        $prefix = 'Administrator\\Model',
        $config = ['ignore_request' => true]
        ) {
            return parent::getModel($name, $prefix, $config);
    }

    public function publish() {
        $model = $this->getModel('Bulletin');
        
        parent::publish();
    }
}

