<?php
/**
 * @package     registrations.Administrator
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
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Services\UserService;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$states = array (
		'0' => Text::_('JUNPUBLISHED'),
		'1' => Text::_('JPUBLISHED'),
		'2' => Text::_('JARCHIVED'),
		'-2' => Text::_('JTRASHED')
);
$editIcon = '<span class="fa fa-pen-square me-2" aria-hidden="true"></span>';

$clientId  = (int) $this->state->get('client_id', 0);
// $user      = Factory::getUser();
$user = UserService::getUser();

?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=bulletins'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		
	<?php else : ?>
		<table class="table" id="bulletinlist">
			<thead>
				<tr>
					<td class="w-5 text-center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</td>
					<th scope="col" style="min-width:85px" class="w-5 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_STATE', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_BULLETIN_TITLE', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-10">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_BULLETIN_TYPE', 'a.category', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-10">
						<?php echo Text::_('COM_JSPORTS_BULLETIN_APPROVED_LABEL'); ?>
					</th>					
					<th scope="col" class="w-40">
						<?php echo Text::_('COM_JSPORTS_BULLETIN_CONTENT'); ?>
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
					<td class="text-center">
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>

<?php 
//     $canChange  = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin; 
    $canChange  = $user->authorise('core.edit.state', 'com_jsports.bulletins.' . $item->id);

?>
                 <td class="text-center">   
                        <?php //echo HTMLHelper::_('jgrid.published', $item->published, $i, 'modules.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i,'bulletins.', $canChange);  ?>
                 
                </td>


					<th scope="row" class="has-context">
						<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_jsports&task=bulletin.edit&id=' . $item->id); ?>">
							<?php echo $this->escape($item->title); ?>
						</a>
					</th>
					<td class="">
						<?php echo JSHelper::translateBulletinCategory($item->category); ?>
					</td>
					<td class="">
						<?php echo JSHelper::translateYesNo($item->approved); ?>
					</td>					
					<td class="">
						<?php echo substr($item->content,0,50); ?>
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