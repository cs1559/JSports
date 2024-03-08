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

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Controller for a single Division
 *
 */
class DivisionController extends FormController
{
    
    /**
     * This function saves a division.  NOT SURE THIS FUNCTION IS TRULY NEEDED.
     * 
     * @param unknown $key
     * @param unknown $urlVar
     */
    public function save($key = null, $urlVar = null) {
        
        parent::save($key, $urlVar);
        
    }
}