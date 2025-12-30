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

namespace FP4P\Component\JSports\Site\View\Myteams;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML Dashboard View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{

    protected $item;
    protected $teams;
    protected $defaultlogo;
    protected $logodir;
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
       
        $params = ComponentHelper::getParams('com_jsports');
        $this->logodir = $params->get('logodir');
        
        $this->teams = $this->get('Teams');
        $this->defaultlogo = '/media/com_jsports/images/swibl-square-logo.png';
        
        return parent::display($tpl);
    }
}


