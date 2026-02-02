<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     0.0.1
 * @package     JSports
 * @subpackage  Divisions.Admin
 * @category    Template
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\UserService;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

/*
$states = array (
		'0' => Text::_('JUNPUBLISHED'),
		'1' => Text::_('JPUBLISHED'),
		'2' => Text::_('JARCHIVED'),
		'-2' => Text::_('JTRASHED')
);
*/

$editIcon = '<span class="fa fa-pen-square me-2" aria-hidden="true"></span>';

$clientId  = (int) $this->state->get('client_id', 0);
// $user      = Factory::getUser();
$user = UserService::getUser();
// $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || is_null($item->checked_out);
//$canChange  = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin;



?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=divisions'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		
	<?php else : ?>
		<table class="table" id="divisionsList">
			<thead>
				<tr>
					<td class="w-5 text-center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</td>
					<th scope="col" style="min-width:85px" class="w-5 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_STATE', 'a.state', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-25">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_PROGRAM', 'b.name', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_AGEGROUP', 'a.agegroup', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_ORDER', 'a.ordering', $listDirn, $listOrder); ?>
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
    $canChange  = $user->authorise('core.edit.state', 'com_jsports.divisions.' . $item->id);

?>
                 <td class="text-center">   
                       
                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i,'divisions.', $canChange);  ?>
                 
                </td>


					<th scope="row" class="has-context">
						<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_jsports&task=division.edit&id=' . $item->id); ?>">
							<?php echo $editIcon; ?><?php echo $this->escape($item->name); ?>
						</a>
					</th>
					<td class="">
						<?php echo $item->program_name; ?>
					</td>
					<td class="">
						<?php echo $item->agegroup; ?>
					</td>
					<td class="">
						<?php echo $item->ordering; ?>
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