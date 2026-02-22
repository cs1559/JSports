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

<form action="<?php echo Route::_('index.php?option=com_jsports&task=closeprogram.process'); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">

<h1>Close Program</h1>

    <p>
        This process will perform all of the close out activities for the
        <strong><?php echo $this->escape($this->item->name); ?></strong>.
        This includes copying the standings to the historical standings table,
        creating a new team record history entry and more.
    </p>

    <input type="hidden" name="programid" value="<?php echo (int) $this->item->id; ?>">

    <button type="submit" class="btn btn-danger">
        <?php echo Text::_('COM_JSPORTS_CLOSE_PROGRAM'); ?>
    </button>
    	<input type="hidden" name="task" value="">
    	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
	
</form>