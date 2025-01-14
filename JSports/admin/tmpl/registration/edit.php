<?php
/**
 * @package     Registration.Administrator
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

$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.registration.script');
$wa->useStyle('com_jsports.jsports.style');


?>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=registration&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="registration-form" class="form-validate form-horizontal">

	<div hidden id="spinner"></div>
	<div class="row">
		<div class="col-md-9">
			<?php echo $this->form->renderField('teamname'); ?>
		</div>
		<div class="col-md-3">
			<?php //echo $this->form->renderField('published')?>
		</div>
	</div>
	
	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_DETAILS')); ?>
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-8">
						<?php echo $this->form->renderField('programid'); ?>
						<?php //echo $this->form->renderField('teamname'); ?>
						<?php echo $this->form->renderField('name'); ?>
						<?php echo $this->form->renderField('address'); ?>
						<?php echo $this->form->renderField('city'); ?>
						<?php echo $this->form->renderField('state'); ?>
						<?php echo $this->form->renderField('email'); ?>
						<?php echo $this->form->renderField('phone'); ?>
						<?php echo $this->form->renderField('cellphone'); ?>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card card-light">
					<div class="card-body">
						<?php echo $this->form->renderField('existingteam'); ?>
						<?php echo $this->form->renderField('teamid'); ?>
						<?php echo $this->form->renderField('grouping'); ?>
						<?php echo $this->form->renderField('skilllevel'); ?>
						<?php echo $this->form->renderField('playoffs'); ?>						
						<?php echo $this->form->renderField('published')?>
						<?php //echo LayoutHelper::render('joomla.edit.global', $this); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', Text::_('COM_JSPORTS_OPTIONS')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">

						<?php echo $this->form->renderField('registeredby'); ?>
						<?php echo $this->form->renderField('ipaddr'); ?>
						<?php echo $this->form->renderField('tosack'); ?>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
						<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
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