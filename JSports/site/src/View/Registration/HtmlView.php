<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\View\Registration;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\TeamService;

/**
 * HTML Registration View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    protected $data;
    protected $program;
    protected $item;
    protected $agreementurl = "";   
    protected $options = null;
    
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
        
        $this->data = $app->getUserState('com_jsports.registration.data', null);

//         $this->programId     = $model->getState('program.id');
//         $this->lastProgramId = $model->getState('program.lastid');
//         $this->returningTeam = $model->getState('team.returning');
//         $this->teamId        = $model->getState('team.id');
   
//         $this->data = $model->getItem();
        $this->form = $model->getForm($this->data, true);
        
        $isNew = false;
        
//         // This user state element is set via the controller.
//         $programid =        Factory::getApplication()->getUserState('com_jsports.edit.registration.programid',0);
//         $registrationid =   Factory::getApplication()->getUserState('com_jsports.edit.registration.registrationid',0);
//         $agreementurl =     Factory::getApplication()->getUserState('com_jsports.edit.registration.agreementurl','');
        
//         if ($programid) {
//             $isNew = true;
//         }
        
        $this->form        = $model->getForm($this->item,true);
        
//         if ($isNew) {
//             $this->item->programid = $this->programId;
//         }
        
        $this->program = ProgramsService::getItem($this->data['programid']);
        
        if (!is_null($this->program->registrationoptions)) {
            $this->options = json_decode($this->program->registrationoptions);
        } 
       
        $this->form->bind($this->item);
        
         // Check for errors.
//         if (count($errors = $this->get('Errors')))
        if (count($errors = $model->getErrors()))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        // $layout = $program->registrationtemplate;
        $layout = $this->options->registrationtemplate;
        
        /**
         * If the program does not have a specific regisration template defined, then use the default
         */
        if (strlen($layout) < 1) {
            $layout = 'default';
        }
        $this->setLayout($layout);
        
        $document = Factory::getApplication()->getDocument();
        $wa = $this->getDocument()->getWebAssetManager();
        $wa->getRegistry()->addExtensionRegistryFile('com_jsports');
        $wa->useScript('com_jsports.phone-formatter.script');
        $wa->useScript('com_jsports.registration.script');
        $phoneSelector = '#jform_phone';
        $document->addScriptOptions('com_jsports.phone', [
            'selector' => $phoneSelector
        ]);
        
        return parent::display($tpl);
    }
}

