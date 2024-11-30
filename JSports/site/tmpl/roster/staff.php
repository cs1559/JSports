<?php
/**
 * @package     Roster.Administrator
 * @subpackage  com_jsports
 * 
 * This template provides a user the ability to EDIT a team record from the website's FRONT END (site).  Most changes to a 
 * team should be done on the administrator site.
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

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.roster.script');

/*
if ($this->item->classification == "S") {
    ?><script>showStaffElements(); </script><?php 
} else {
    ?><script>hideStaffElements(); </script><?php
}
*/

?>

<h1><?php //echo $this->team->name; ?> Add/Edit Staff Member </h1>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=roster&layout=staff&id=' . (int) $this->item->id); ?>"
	method="post" name="teamForm" id="team-form" class="form-validate">
	
		<?php echo $this->form->renderField('id'); ?>
		<?php echo $this->form->renderField('teamid'); ?>
		<?php echo $this->form->renderField('programid'); ?>
		<?php //echo $this->form->renderField('classification'); ?>	
		<input type="hidden" id="jform_classification" name="jform[classification]" value="S">			
		<?php echo $this->form->renderField('firstname'); ?>
		<?php echo $this->form->renderField('lastname'); ?>
		<?php //echo $this->form->renderField('playernumber'); ?>
		<?php echo $this->form->renderField('role'); ?>
		<?php echo $this->form->renderField('userid'); ?>
		<?php echo $this->form->renderField('staffadmin'); ?>
		<?php echo $this->form->renderField('email'); ?>
		<input type="hidden" id="jform_playernumber" name="jform[playernumber]" value="0">

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
	
	<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="roster.save">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="roster.cancel" formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
</div>
	
	
</form>