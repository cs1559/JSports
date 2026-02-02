<?php
/**
 * @package     Games.Administrator
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
use FP4P\Component\JSports\Site\Services\UserService;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$editIcon = '<span class="fa fa-pen-square me-2" aria-hidden="true"></span>';

$clientId  = (int) $this->state->get('client_id', 0);
// $user      = Factory::getUser();
$user = UserService::getUser();

$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.game.script');
$wa->useStyle('com_jsports.jsports.style');

// $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || is_null($item->checked_out);
//$canChange  = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin;

?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=games'); ?>" method="post" name="adminForm" id="adminForm">


	<?php 
	
//echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	<div hidden id="spinner"></div>
	<div class="selection-criteria row" style="display: flex; flex-direction: row; ">
	
		<div class="w-20">
   		<?php echo $this->filterForm->renderField('gameid','filter'); ?>
		</div>
		<div class="w-20">
   		<?php echo $this->filterForm->renderField('programid','filter'); ?>	
		</div>
	
		<div class="w-20">
			<?php echo $this->filterForm->renderField('divisionid','filter'); ?> 		
		</div>	
		
		<div class="w-20">
			<?php echo $this->filterForm->renderField('teamid','filter'); ?> 		
		</div>	
		
		<div class="w-20">
			<button style="margin-top: 30px;" type="submit" class="btn btn-primary validate" >
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('COM_JSPORTS_SEARCH'); ?>
    	    </button>
		</div>
		
		
	</div>
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		
	<?php else : ?>
		<table class="table" id="gameList">
			<thead>
				<tr>
					<td class="w-3 text-center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</td>
					<th scope="col" class="w-8">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_GAMEDATE', 'a.gamedate', $listDirn, $listOrder); ?>
					</th>					
					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-10">
						<?php echo Text::_('COM_JSPORTS_GAMESTATUS'); ?>
					</th>					
					
					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_HOMETEAMNAME', 'a.hometeamname', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-5">
						<?php echo Text::_('COM_JSPORTS_HOMETEAMSCORE'); ?>
					</th>					

					<th scope="col" class="w-20">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_AWAYTEAMNAME', 'a.hometeamname', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-5">
						<?php echo Text::_('COM_JSPORTS_AWAYTEAMSCORE'); ?>
					</th>					
					<th scope="col" class="w-5">
						<?php echo Text::_('State'); ?>
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
    $canChange  = $user->authorise('core.edit.state', 'com_jsports.programs.' . $item->id);

?>
                 <td class="text-center">   
                        <?php echo $item->gamedate; ?>                 
                </td>


					<th scope="row" class="has-context">
						<a class="hasTooltip" href="<?php echo Route::_('index.php?option=com_jsports&task=game.edit&id=' . $item->id); ?>">
							<?php echo $editIcon; ?><?php echo $this->escape($item->name); ?>
						</a>
					</th>
					<td class="">
						<?php echo $item->gamestatus; ?>
					</td>
					<td class="">
						<?php echo $item->hometeamname; ?>
					</td>
					
					<td class="">
						<?php echo $item->hometeamscore; ?>
					</td>

					<td class="">
						<?php echo $item->awayteamname; ?>
					</td>

					<td class="">
						<?php echo $item->awayteamscore; ?>
					</td>
					<td class="">
						<?php echo $item->published; ?>
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