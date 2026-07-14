<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */
namespace FP4P\Component\JSports\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
// use Joomla\CMS\Component\ComponentHelper;
// use Joomla\CMS\Uri\Uri;
use FP4P\Component\JSports\Site\Services\SecurityService;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Default JSports SITE Component Controller. Resolves the requested view and,
 * for the 'bulletins' view specifically, gates access behind a permission
 * check before delegating to the base controller's display logic.
 *
 * @since  1.5
 */
class DisplayController extends BaseController
{
    
    protected $default_view = 'dashboard';

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached.
     * @param   array    $urlparams  An array of safe URL parameters and their variable
     *                               types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  static|$this  This object to support chaining. Returns early
     *                        (without calling the parent) if the requested
     *                        view is 'bulletins' and the user lacks permission.
     *
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = array())
    {
        
        $app = Factory::getApplication();
        //$user = $app->getIdentity();
        
//         $uri = Uri::getInstance();
//         $params = ComponentHelper::getParams('com_jsports');
//         $itemid = $params->get('itemid');
//         $uri->setVar('Itemid', $itemid);

        // What view is being requested?
        $view = $app->input->getCmd('view', $this->default_view);

        if ($view === 'bulletins') {
            if (! SecurityService::canManageBulletins()) {
                $this->setMessage(Text::sprintf('COM_JSPORTS_INSUFFICIENT_PERMISSION','Bulletins'),'error');
                $this->setRedirect(Route::_('index.php?option=com_jsports&view=standings', false));
                return $this;
            }
        }


	    return parent::display($cachable, $urlparams);
	}
}
