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

namespace FP4P\Component\JSports\Site\View\Logoupload;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use FP4P\Component\JSports\Administrator\Table\TeamsTable;

/**
 * HTML Logo Upload View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{

    /** @var TeamsTable */
    protected $item;
    
    /** @var string  Full path of the team's logo image */
    protected $teamlogo;
    
    /** @var string  Team Name */
    protected $teamname;
    
    public function display($tpl = null)
    {
        /** @var \FP4P\Component\JSports\Site\Model\LogouploadModel $model */
        $model            = $this->getModel();
        
        $this->state      = $model->getState();
        $this->item       = $model->getItem();
        $this->form       = $model->getForm($this->item,true);
       
        //@TODO  This hardcoded value needs to be modified to be configurable from the component options feature of Joomla
        $this->teamlogo = "/media/com_jsports/images/swibl-large.png";
        
        $this->teamname = $this->item->name;
        
        $this->form->bind($this->item);        
        
        // Check for errors.
        $errors = $model->getErrors();
        if (!empty($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
    }
}

