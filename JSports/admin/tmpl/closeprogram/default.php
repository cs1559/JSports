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

<form action="<?php echo Route::_('index.php?option=com_jsports&view=league&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="closeprograms-form" class="form-validate">

	<h1>Close Program</h1>
	<p>This process will perform all of the close out activities for the <strong><?php echo $this->item->name; ?></strong>.  This includes copying the standings
	to the historical standings table, creating a new team record history entry and more.
	</p>
	
	<a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_jsports&task=closeprogram.process&id=' . (int) $this->item->id); ?>">CLICK HERE TO CLOSE PROGRAM</a>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>