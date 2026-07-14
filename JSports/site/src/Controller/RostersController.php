<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace FP4P\Component\JSports\Site\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Component\ComponentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller for the team ROSTERS list view (cancel-edit action).
 *
 * NOTE: this class was previously (incorrectly) named `RosterController`,
 * identical to the class in RosterController.php in this same namespace.
 * Two classes with the same fully-qualified name is a fatal PHP error if
 * both files are ever loaded in the same request, and it also meant Joomla's
 * MVC factory could never resolve a controller actually named
 * `RostersController`. Renamed to match the file name.
 *
 * @since  1.6
 */
class RosterController extends BaseController
{
    
    /**
     * Cancels a roster-list edit action, clearing the session edit state and
     * redirecting back to the team's profile page.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function cancel()
    {
        // Check for request forgeries.
        $this->checkToken();

        $app    = $this->app;
        // Get the user data.
        $requestData = $app->getInput()->post->get('jform', [], 'array'); 
        $teamid = $requestData['teamid'];
        
        // Flush the data from the session.
        $this->app->setUserState('com_jsports.edit.team.data', null);
        
        // Redirect to user profile.
        $this->setRedirect(Route::_('index.php?option=com_jsports&view=team&id=' . $teamid, false));
    }
}
