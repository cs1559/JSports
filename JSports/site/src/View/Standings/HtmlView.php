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

namespace FP4P\Component\JSports\Site\View\Standings;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Model\DashboardModel;
use Joomla\CMS\Input\Input;


/**
 * HTML Standings View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The item object details
     *
     * @var    \JObject
     * @since  1.6
     */
    protected $standings;
    protected $program;

    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        $user       = $this->getCurrentUser();
        $input = Factory::getApplication()->input;
        $programid = (int) $input->get('programid');
        
        if ($programid > 0) {
            $this->standings = $this->get('ProgramStandings');
            $this->program = $this->get('Program');
            $layout = "show";
        }
        
        return parent::display($tpl);
    }
}