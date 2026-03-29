<?php
/**
 * @package     Campaign.Administrator
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
use Joomla\CMS\Factory;


HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('jquery.framework');

$token = Factory::getApplication()->getFormToken(); // e.g. "123abc..."
?>
<script>
window.JSportsDefaults = {
  sponsorshipid: "<?php echo (int)($this->item->sponsorshipid ?? 0); ?>",
  assetid: "<?php echo (int)($this->item->assetid ?? 0); ?>"
};
</script>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=campaign&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="campaign-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_DETAILS')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div>
						<?php echo $this->form->renderField('sponsorid'); ?>
						<?php echo $this->form->renderField('sponsorshipid'); ?>
						<?php echo $this->form->renderField('layout'); ?>
						<?php //echo $this->form->renderField('campaigntype'); ?>
						<?php echo $this->form->renderField('startdate'); ?>
						<?php echo $this->form->renderField('enddate'); ?>
						<?php echo $this->form->renderField('positions'); ?>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
						<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
						<?php echo $this->form->renderField('impressions'); ?>
						<?php echo $this->form->renderField('clicks'); ?>						
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'contents', Text::_('COM_JSPORTS_CAMPAIGN_CONTENT')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
						<?php echo $this->form->renderField('content'); ?>
				</div>  
			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'image', Text::_('COM_JSPORTS_CAMPAIGN_IMAGE')); ?>
		<div class="row">
			<div class="col-md-6">
				<div class="row">
						<?php echo $this->form->renderField('imageid'); ?>
				</div>  
			</div>
			<div class="col-md-6">
			<h2>Current Image</h2>
			<?php // if (!empty($this->imageurl)) {?>
				<div class="campaign-image-wrapper">  <!-- GOOD -->
					<img class="campaign-image img-responsive"
						src="<?php echo $this->imageurl; ?>"/>
				</div>  <!-- GOOD -->
				
				<hr/>
			<?php //} ?>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>
		
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'links', Text::_('COM_JSPORTS_CAMPAIGN_LINKS')); ?>
		<div class="row">
			<div class="col-md-6">
				<div class="row">
						<?php echo $this->form->renderField('link'); ?>
						<?php echo $this->form->renderField('assetid'); ?>
						<?php echo $this->form->renderField('url'); ?>
				</div>  
			</div>
			<div class="col-md-6">
				<div class="card card-light">
					<div class="card-body">
					<strong>REDIRECT LINKS INFORMATION:</strong>
					<hr/>
					<ul>
						<li><strong>Sponsor Website</strong> - This option will use the sponsors website property in the link used when rendering the campaign.</li>
						<li><strong>Sponsor Asset/Document</strong> - This option will link to an uploaded asset/document from the Sponsors section.</li>
						<li><strong>External URL</strong> - this option will allow you to include an external URL in the link when someone clicks on the campaign.</li>
					</ul>
					<strong>NOTE:  Only ONE link will be used when the campaign renders. </strong>
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>


		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'customcss', Text::_('COM_JSPORTS_CUSTOMCSS')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
						<?php echo $this->form->renderField('classname'); ?>
						<?php echo $this->form->renderField('customcss'); ?>
					</div>
				</div>  
			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
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