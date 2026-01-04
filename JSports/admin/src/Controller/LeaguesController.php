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

/**
 * ================================================================================
 * LEAGUES MAY NOT BE REQUIRED AND MAYBE REMOVED IN A LATER RELASE.  MANY OF THE
 * OPTIONS ASSOCIATED WITH A LEAGUE ARE SUPPORTED AT THE PROGRAM LEVEL OR AT THE 
 * COMPONENT LEVEL.
 * ================================================================================ 
 */

namespace FP4P\Component\JSports\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class LeaguesController extends AdminController
{
    protected $default_view = 'leagues';
    
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  The array of possible config values. Optional.
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     *
     * @since   1.6
     */
    public function getModel($name = 'League', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
    
}
