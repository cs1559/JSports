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

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;

class ProgramsController extends AdminController
{
    protected $default_view = 'programs';
    
    public function display($cachable = false, $urlparams = array())
    {
        
        return parent::display($cachable, $urlparams);
    }

    
    public function publish() {
        $model = $this->getModel('Program');
        
        parent::publish();
    }

    public function getModel($name = 'Program', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
 
    public function close() {
        $ids = $this->input->post->get('cid', array(), 'array');
        if (count($ids) > 1) {
            Factory::getApplication()->enqueueMessage("You can only close one program at a time", 'warning');
            $this->setRedirect('index.php?option=com_jsports&view=programs');
        } else {
            $this->setRedirect('index.php?option=com_jsports&view=closeprogram&id=' . $ids[0]);
        }
    }
}
