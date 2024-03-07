<?php
/**
 * @package     Venues.Administrator
 * @subpackage  com_jsports
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * CHANGE HISTORY:
 * 2024-02-29  Changed the action value to '#' and commented out the Route statement
 */



defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$clientId  = (int) $this->state->get('client_id', 0);
$user      = Factory::getUser();


?>

<h1>League Venues</h1>
<form action="#<?php //echo Route::_('index.php?option=com_jsports&view=venues'); ?>" method="post" name="adminForm" id="adminForm">
	<?php // echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	
	 	<div class="row">
			<div class="col-sm-3">
				<?php echo $this->filterForm->renderField('search', 'filter'); ?>
			</div>
			<div class="col-sm-3">
				<?php echo $this->filterForm->renderField('limit','list');?>
			</div>
	 	</div>
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		
	<?php else : ?>
		<table class="table" id="venuesList">
			<thead>
				<tr>
					<th scope="col" class="w-30">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_NAME', 'a.name', $listDirn, $listOrder); ?>
					<th scope="col" class="w-20">
					</th>	
					
					<th scope="col" class="w-15">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_CITY', 'a.contactname', $listDirn, $listOrder); ?>
					</th>					
					<th scope="col" class="w-10">
						<?php echo Text::_('COM_JSPORTS_STATE');  ?>
					</th>					
					
					<th scope="col" class="w-5 d-none d-md-table-cell">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			
			$n = count($this->items);
			foreach ($this->items as $i => $item) :
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<th scope="row" class="has-context">
					
						<?php echo $this->escape($item->name); ?>
					</th>
					<td class="">
						<?php echo $item->address1; ?>
					</td>
					<td class="">
						<?php echo $item->city; ?>
					</td>
					
					<td class="">
						<?php echo $item->state; ?>
					</td>

					<td class="">
						<?php echo $item->id; ?>
					</td>
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>

		<?php // load the pagination. ?>
		<?php echo $this->pagination->getListFooter(); ?>

	<?php endif; ?>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>