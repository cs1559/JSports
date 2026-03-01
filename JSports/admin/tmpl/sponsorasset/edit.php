<?php
/**
 * @package     Registration.Administrator
 * @subpackage  com_jsports
 *
 * @copyright   Copyright (C) 2024 - 2026 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.registration.script');
$wa->useStyle('com_jsports.jsports.style');

$app = \Joomla\CMS\Factory::getApplication();

?>

<form enctype="multipart/form-data" action="<?php echo Route::_('index.php?option=com_jsports&view=sponsorasset&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="sponsorasset-form" class="form-validate form-horizontal">

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
						<?php echo $this->form->renderField('id'); ?>
						<?php echo $this->form->renderField('sponsorid'); ?>
						<?php echo $this->form->renderField('description'); ?>
						<?php echo $this->form->renderField('assettype'); ?>
						<?php echo $this->form->renderField('filename'); ?>
						<?php echo $this->form->renderField('mimetype'); ?>
						<?php echo $this->form->renderField('afile'); ?>
						<?php echo $this->form->renderField('height'); ?>
						<?php echo $this->form->renderField('width'); ?>
					</div>
				</div>

			</div>
			<div class="col-md-3">
				<div class="card card-light">
					<div class="card-body">
							<img class="card-img-top" 
      							src="<?php echo SponsorHelper::getAssetURL($this->item->sponsorid, $this->item->filename);?>?t=<?php echo time(); ?>" 
      								style="height: 180px; object-fit: cover;"/>
					</div>
				</div>
			</div>
		</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="return" value="<?php echo $app->input->getBase64('return'); ?>">+
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>