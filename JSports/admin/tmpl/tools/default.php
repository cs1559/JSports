<?php
/**
 * @package     Tools.Administrator
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

<h2>JSports Tools</h2>
<p>
These are a collection of tools that can be used to manage your JSports installation.
</p>



    <table>
        <tr><td><a href="index.php?option=com_jsports&task=tools.newsletterImport"><button class="btn btn-primary">Newsletter Import</button></a></td><td>Import Contact Emails into Newsletter Program</td></tr>
        <tr><td><a href="index.php?option=com_jsports&task=tools.purgeLogs"><button class="btn btn-primary">Purge Log records</button></a></td><td>Purge recoreds from the JSports log table.</td></tr>
        <tr><td><a href="index.php?option=com_jsports&task=tools.refreshStandings"><button class="btn btn-primary">Refresh Standings</button></a></td><td>Purge recoreds from the JSports log table.</td></tr>
    </table>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=tools'); ?>" method="post" name="adminForm" id="adminForm">

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
	
</form>