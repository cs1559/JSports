<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
namespace FP4P\Component\JSports\Site\View\Bulletin;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML Bulletin View
 * 
 * - 01/15/2026 - Refactored to change $this->get to $model->getxxx to commply with future Joomla releases.
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    protected $program;
    protected $item;
    protected $form;
    protected $team;
    protected $state;
    protected $attachmentsenabled = false;
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
//         $app = Factory::getApplication();
        $params = ComponentHelper::getParams('com_jsports');
        $this->attachmentsenabled = (bool) $params->get('bulletinattachments');
        
        /** @var \FP4P\Component\JSports\Site\Model\BulletinModel $model */
        $model = $this->getModel('Bulletin');

        $this->state = $model->getState();
        $this->item  = $model->getItem();
        $this->form  = $model->getForm($this->item,true);      
        $this->team  = $model->getTeam(); 

//         $isNew = false;
//         $programid = Factory::getApplication()->getUserState('com_jsports.edit.bulletin.programid',0);
//         if ($programid) {
//             $isNew = true;
//         }
                  
//         if ($isNew) {
//             $this->item->programid = $programid;
//         }
                
         // Check for errors.
        $errors = $model->getErrors();
        if (!empty($errors)) 
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
    }
}
