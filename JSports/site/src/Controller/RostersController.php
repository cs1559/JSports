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

namespace FP4P\Component\JSports\Site\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Input\Input;

use FP4P\Component\JSports\Site\Services\ProgramsService;
use Joomla\CMS\Component\ComponentHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Controller object for an individiaul ROSTERs entry
 *
 * @since  1.6
 */
class RosterController extends BaseController
{
    
    
    
    /**
     * Method to cancel an edit.
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
