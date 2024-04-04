<?php
/**
 * @package     Programs.ministrator
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

<form action="<?php echo Route::_('index.php?option=com_jsports&view=program&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="programs-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_DETAILS')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
						<?php echo $this->form->renderField('leagueid'); ?>
						<?php echo $this->form->renderField('description'); ?>
						<?php echo $this->form->renderField('sportcode'); ?>
						<?php echo $this->form->renderField('groupingscode'); ?>
						<?php echo $this->form->renderField('programstart'); ?>
						<?php echo $this->form->renderField('programend'); ?>						
						<?php echo $this->form->renderField('id'); ?>
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
	

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', Text::_('COM_JSPORTS_OPTIONS')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
						<?php echo $this->form->renderField('registrationonly'); ?>
						<?php echo $this->form->renderField('active'); ?>
						<?php echo $this->form->renderField('status'); ?>
						<?php echo $this->form->renderField('publishstandings'); ?>
						<?php echo $this->form->renderField('setupfinal'); ?>
						<?php //echo $this->form->renderField('rostersenabled'); ?>
						<?php echo $this->form->renderField('rosterslocked'); ?>
						<?php echo $this->form->renderField('standingspolicy'); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'registration', Text::_('COM_JSPORTS_REGISTRATION')); ?>
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-6">
						<?php echo $this->form->renderField('registrationonly'); ?>
						<?php echo $this->form->renderField('registrationopen'); ?>
						<?php echo $this->form->renderField('registrationtemplate'); ?>
						<?php echo $this->form->renderField('registrationstart'); ?>
						<?php echo $this->form->renderField('registrationend'); ?>

					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-6">
				<?php echo $this->form->renderField('registrationoptions'); ?>
				</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'workflow', Text::_('COM_JSPORTS_WORKFLOW')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
                         <ul>
                          <li>Registration</li>
                          <li>Setup League/Program</li>
                          <li>Lock Program Setup</li>
                          <li>Set Program Status/Active</li>
                          <li>Lock Rosters</li>
                          <li>Close Season</li>
                        </ul> 
					</div>
				</div>  
			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
						<?php // echo LayoutHelper::render('joomla.edit.global', $this); ?>
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