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

namespace FP4P\Component\JSports\Site\View\Logoupload;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * HTML Logo Upload View
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{

    protected $item;     
    protected $teamlogo;
    protected $teamname;
    
    public function display($tpl = null)
    {
        $this->data       = $this->get('Data');
        $this->state      = $this->get('State');
        $this->item       = $this->get('Item');
        
        $this->form       = $this->getModel()->getForm($this->item,true);
       
        //@TODO  This hardcoded value needs to be modified to be configurable from the component options feature of Joomla
        $this->teamlogo = "/media/com_jsports/images/swibl-large.png";
        
        $this->teamname = $this->item->name;
        
         $this->form->bind($this->item);        
        
         // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
    }
}

