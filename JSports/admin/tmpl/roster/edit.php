<?php
/**
 * @package     Team.Administrator
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

<form action="<?php echo Route::_('index.php?option=com_jsports&view=roster&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="team-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_DETAILS')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
		<?php echo $this->form->renderField('id'); ?>
		<?php echo $this->form->renderField('teamid'); ?>
		<?php echo $this->form->renderField('programid'); ?>
		<?php echo $this->form->renderField('classification'); ?>				
		<?php echo $this->form->renderField('firstname'); ?>
		<?php echo $this->form->renderField('lastname'); ?>
		<?php echo $this->form->renderField('playernumber'); ?>
		<?php echo $this->form->renderField('role'); ?>
		<?php echo $this->form->renderField('userid'); ?>
		<?php echo $this->form->renderField('staffadmin'); ?>
		<?php echo $this->form->renderField('email'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>