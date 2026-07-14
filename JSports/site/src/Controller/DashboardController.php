<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */
namespace FP4P\Component\JSports\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

/**
 * JSports SITE Component Controller for the member Dashboard view.
 *
 * NOTE: this class was previously (incorrectly) named `DisplayController`,
 * identical to the class in DisplayController.php in this same namespace.
 * Two classes with the same fully-qualified name is a fatal PHP error if
 * both files are ever loaded in the same request, and it also meant Joomla's
 * MVC factory could never resolve a controller actually named
 * `DashboardController`. Renamed to match the file name and its apparent
 * intent (@see $default_view below).
 *
 * @since  1.5
 */
class DashboardController extends BaseController
{
    
    protected $default_view = 'dashboard';
  
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types,
	 *             for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = array())
	{
	    $uri = Uri::getInstance();
	    $params = ComponentHelper::getParams('com_jsports');
	    $itemid = $params->get('itemid');
	    $uri->setVar('Itemid', $itemid);
	    
	    return parent::display($cachable, $urlprams);
	}
}
