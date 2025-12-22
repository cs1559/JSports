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

<form enctype="multipart/form-data" action="<?php echo Route::_('index.php?option=com_jsports&view=bulletin&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="bulletin-form" class="form-validate form-horizontal">

	<div hidden id="spinner"></div>
	<div class="row">
		<div class="col-md-8">
			<?php echo $this->form->renderField('title'); ?>
		</div>
		<div class="col-md-4">
			<?php //echo $this->form->renderField('published')?>
		</div>
	</div>
	
	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_DETAILS')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="">
						<?php echo $this->form->renderField('bulletintype'); ?>
						<?php echo $this->form->renderField('content'); ?>
						<?php echo $this->form->renderField('location'); ?>
						<?php //echo $this->form->renderField('startdate'); ?>
						<?php //echo $this->form->renderField('enddate'); ?>
						<?php echo $this->form->renderField('externalurl'); ?>

					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
						<?php echo $this->form->renderField('id'); ?>
						<?php echo $this->form->renderField('teamid'); ?>				
						<?php echo $this->form->renderField('published')?>
						<?php //echo LayoutHelper::render('joomla.edit.global', $this); ?>
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', Text::_('COM_JSPORTS_AUDIT_INFO')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
						<?php echo $this->form->renderField('ownerid'); ?>
						<?php //echo $this->form->renderField('createdate'); ?>
						<?php //echo $this->form->renderField('updatedate'); ?>
						<?php echo $this->form->renderField('updatedby'); ?>
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

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', Text::_('COM_JSPORTS_ATTACHMENT')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8"> 
						<div class="control-group">
							<div class="control-label">
  								<?php echo $this->form->getLabel('attachment'); ?>
							</div>
                            <div class="controls">
                            <?php if ($this->item->hasAttachment) { ?>
                                <a href="<?php echo $this->item->attachmentUrl;?>" target="_blank"><?php echo $this->item->attachment; ?></a>
                            	<a href="<?php echo Route::_('index.php?option=com_jsports&task=bulletin.deleteAttachment&id=' . (int) $this->item->id); ?>">[Remove Attachment]</a>
                            	<?php } else {
                            	   echo "No attachments";
                            	}?>					
                            </div>
						</div>
						<?php echo $this->form->renderField('afile'); ?>
						
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