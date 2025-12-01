<?php
/**
 * @package     programsetup.Administrator
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
use FP4P\Component\JSports\Administrator\Helpers\Html;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$editIcon = '<span class="fa fa-pen-square me-2" aria-hidden="true"></span>';

$clientId  = (int) $this->state->get('client_id', 0);
$user      = Factory::getUser();

?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=programsetup'); ?>" method="post" name="adminForm" id="adminForm">
	   
	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
    		
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		
	<?php else : ?>
		<table class="table" id="programList">
			<thead>
				<tr>
					<td class="w-5 text-center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</td>
					<th scope="col" style="min-width:85px" class="w-5 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_STATE', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_TEAMNAME', 'c.teamname', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-5">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_AGEGROUP', 'r.agegroup', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-22">
						<?php echo Text::_('COM_JSPORTS_NAME'); ?>
					</th>									
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_PROGRAM'); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_ASSIGNMENT'); ?>
					</th>
					
					<th scope="col" class="w-5 d-none d-md-table-cell">
						<?php //echo HTMLHelper::_('searchtools.sort', 'R', 'a.id', $listDirn, $listOrder); 
	                       echo Text::_('ID');
	                   ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			
			$n = count($this->items);
			foreach ($this->items as $i => $item) :
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="text-center">
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>

<?php 

    $canChange  = $user->authorise('core.edit.state', 'com_jsports.programsetup.' . $item->id);

?>
                 <td class="text-center">   
                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i,'programsetup.', $canChange);  ?>
                </td>


					<td scope="row" class="has-context">
						<?php echo $this->escape($item->teamname) . ' (' . $item->teamid . ')'; ?>
					</td>
					<td class="">
						<?php echo $item->grouping; ?>
					</td>
					<td class="">
						<?php echo $item->contactname; ?>
					</td>
					
					<td class="">
						<?php echo $item->programname; ?>
					</td>

					<td class="">
						<?php 
						  echo Html::getProgramDivisions( $item->programid, $item->id, $item->divisionid);
						?>
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