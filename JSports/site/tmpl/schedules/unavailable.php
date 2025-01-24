<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     0.0.1
 * @package     JSports
 * @subpackage  Schedules.Site
 * @category    Templates
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 *
 * CHANGES:
 * 2024-02-02   Removed ID from the display list.
 * 2024-02-13   Remove d W/L and score info.  replaced with location and time.
 *      - Reversed "Home @ Away" to "Away @ Home"
 * 2024-02-22   Changed logic on when to display the EDIT/DELETE buttons.
 * 2024-03-01   Added DIV with jsports-table-wrapper to make table responsive.
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;

$clientId  = (int) $this->state->get('client_id', 0);
$user      = Factory::getUser();

// Set Page Title and load specific stylesheet
$document = Factory::getDocument();

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.jsports.style');
$wa->useStyle('com_jsports.teamprofile.style');

$document->setTitle(Text::_('COM_JSPORTS_TEAMSCHEDULE_PAGE_TITLE'));

?>

<h2>MANAGE SCHEDULE IS CURRENTLY UNAVAILBLE </H2>

The reason is the current program (<?php echo $this->program->name; ?>) does not support this function or the program is not active.

<br/><br/>
<p>
<a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $this->team->id); ?>">Go Back to Team Profile </a>
</p>
