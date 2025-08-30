<?php
/**
 * @package     Leagues.Administrator
 * @subpackage  com_jsports
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=tools' ); ?>"
	method="post" name="adminForm" id="auditprofiles-form" class="form-validate">

	<h1>Audit Team Profiles</h1>
	<p>This process will perform an audit of team profiles.  This will evaluate 1)  Email addresses, 2) Rosters and 3) Schedules.   
	</p>
	
	<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_jsports&task=auditteamprofiles.process'); ?>">CLICK HERE TO AUDIT PROFILES</a>
<!-- 	<input type="hidden" name="task" value=""> -->
	<?php echo HTMLHelper::_('form.token'); ?>
</form>