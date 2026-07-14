<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\View\Registrationcomplete;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\TeamService;

/**
 * HTML Registration Complete View
 * This view presents registration information to the user after the registration has been save.
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    protected $data;
    protected $programname;
    
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        
        /** @var \FP4P\Component\JSports\Administrator\Model\RegistrationModel $model */
        $model = $this->getModel();
        
        $this->data = $model->getItem();
        $this->programname = $model->getProgramName();
                
        return parent::display($tpl);
    }
}

