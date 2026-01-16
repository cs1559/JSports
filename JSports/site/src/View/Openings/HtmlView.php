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

namespace FP4P\Component\JSports\Site\View\Openings;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\ProgramsService;
use FP4P\Component\JSports\Site\Services\SecurityService;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Pagination\Pagination;
use FP4P\Component\JSports\Site\Model\OpeningsModel;

/**
 * HTML View for roster openings report
 * 
 * @author Chris Strieter
 *
 */
class HtmlView extends BaseHtmlView
{
    public $form;
    
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;
    
    /**
     * The model state
     */
    protected $state;
           
    public function display($tpl = null)
    {
        /** @var OpeningsModel $model */
        $model = $this->getModel();
        
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();  
        $this->state         = $model->getState();
        
        // Check for errors.
        $errors = $model->getErrors();
        if (!empty($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }
        
        return parent::display($tpl);
        
    }
    
}

