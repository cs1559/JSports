<?php
/**
 * @package     Teams.Site
 * @subpackage  com_jsports
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/*
 * CHANGE HISTORY:
 * 2024-03-01  Removed/commented the sortable column headers in the table for division name and city.
 */


defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$editIcon = '<span class="fa fa-pen-square me-2" aria-hidden="true"></span>';

$clientId  = (int) $this->state->get('client_id', 0);

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.teamlist.style');

?>

<form action="#<?php //echo Route::_('index.php?option=com_jsports&view=teams'); ?>" method="post" name="adminForm" id="adminForm">
	
	<div class="row">
		<div class="col-sm-3">
			<?php echo $this->filterForm->renderField('programid', 'filter'); ?>
		</div>
		<div class="col-sm-3">
			<?php echo $this->filterForm->renderField('divisionid', 'filter'); ?>
		</div>
		<div class="col-sm-3">
			<?php echo $this->filterForm->renderField('teamid', 'filter'); ?>
		</div>
		<div class="col-sm-3">
			<?php echo $this->filterForm->renderField('viewtype', 'filter'); ?>
		</div>
		<?php echo $this->filterForm->renderField('limit', 'list'); ?>	
	
	</div>

<?php 
    if ($this->isProgramPending) {
        ?>
        <br/>
        <div class="alert alert-warning" role="alert">
            <strong><?php echo Text::_('COM_JSPORTS_PROGRAM_PENDING'); ?></strong>
        </div>
        <?php 
    }
?>
<div class="container">
	<h2>Completed Games - <?php echo $this->program->name; ?></h2>
	
	<div class="jsports-table-wrapper">
	<table class="table">
  	<thead>
    	<tr>
      		<th class="w-10" scope="col">Date</th>
      		<th class="w-30" scope="col">Game</th>
      		<th class="w-30" scope="col">Home Score</th>
	  		<th class="w-30" scope="col">Away Score</th>	
	  		<th class="w-30" scope="col">Status</th>
      		<th class="w-20" scope="col">Division</th>
    	</tr>
  	</thead>

	<?php foreach ($this->items as $game) {	?>
		<tr>
			<th scope="row"><?php echo $game->gamedate; ?></th>
			<td><?php echo $game->name; ?></td>
			<td><?php echo $game->hometeamscore; ?></td>
			<td><?php echo $game->awayteamscore; ?></td>
			<td><?php echo $game->gamestatus; ?></td>
			<td><?php echo $game->divisionname; ?></td>
		</tr>
	<?php } ?>

	</table>
	</div>
</div>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>