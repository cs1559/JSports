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

$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.game.script');
$wa->useStyle('com_jsports.jsports.style');

?>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=game&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="game-form" class="form-validate">

	<?php 
	//echo "DivID=" . $this->state->get('divisionid');
	
	echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div hidden id="spinner"></div>
	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_MATCH')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
					<?php echo $this->form->renderField('programid'); ?>
					<?php echo $this->form->renderField('divisionid'); ?>
					<?php echo $this->form->renderField('teamid'); ?>
						<?php //echo $this->form->renderField('hometeamid'); ?>
						<?php //echo $this->form->renderField('awayteamid'); ?>
						<?php echo $this->form->renderField('opponentid'); ?>
						<?php echo $this->form->renderField('location'); ?>
						<?php echo $this->form->renderField('gamedate'); ?>
						<?php echo $this->form->renderField('gametime'); ?>
						<?php echo $this->form->renderField('leaguegame'); ?>
						<?php echo $this->form->renderField('gamestatus'); ?>
						
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


		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', Text::_('COM_JSPORTS_RESULT')); ?>
		<div class="row">
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-8">
						<?php echo $this->form->renderField('hometeamscore'); ?>
						<?php echo $this->form->renderField('awayteamscore'); ?>
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