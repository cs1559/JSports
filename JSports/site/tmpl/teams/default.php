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
use FP4P\Component\JSports\Site\Objects\RouteHelper;

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
			<?php echo $this->filterForm->renderField('search', 'filter'); ?>
		</div>

		<div class="col-sm-3">
			<?php echo $this->filterForm->renderField('fullordering', 'list'); ?>
		</div>
		<?php echo $this->filterForm->renderField('limit', 'list'); ?>	
	
	</div>
	
	<h3>Teams</h3>
		
	<div class="teamlist-table-wrapper">
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		
	<?php else : ?>
		<table class="table table-striped" id="teamsList">

			<thead>
				<tr>
					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_NAME', 'a.name', $listDirn, $listOrder) . '<br/>'; 
						
						?>
						
					</th>
					<th scope="col" class="w-20">
						<?php echo Text::_('COM_JSPORTS_CONTACTNAME'); ?>
					</th>
					<th scope="col" class="w-20">
						<?php //echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_DIVISION', 'c.divisionname', $listDirn, $listOrder); ?>
						<?php echo Text::_('COM_JSPORTS_DIVISION'); ?>
					</th>
									
					<th scope="col" class="w-20 ">
						<?php //echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_CITY', 'a.city', $listDirn, $listOrder); ?>
						<?php echo Text::_('COM_JSPORTS_CITY'); ?>
					</th>
					
				</tr>
			</thead>
			<tbody>
			<?php
			
			$n = count($this->items);
			foreach ($this->items as $i => $item) :
				?>
					<th scope="row" class="has-context">
						<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $item->id); ?>">
							<?php echo $this->escape($item->name); ?>
						</a><br/>
					</th>
					<td class="">
						<?php echo $item->contactname; ?>
					</td>
					<td class="">
						<?php echo $item->divisionname; ?>
					</td>					
					<td class="">
						<?php echo $item->city; ?>
					</td>
					
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>

		<?php // load the pagination. ?>
		<?php echo $this->pagination->getListFooter(); ?>
	
	</div>

	<?php endif; ?>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>