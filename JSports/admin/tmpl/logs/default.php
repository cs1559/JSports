<?php
/**
 * @package     Logs.Administrator
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
use FP4P\Component\JSports\Site\Services\UserService;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$clientId  = (int) $this->state->get('client_id', 0);
// $user      = Factory::getUser();
$user = UserService::getUser();

?>

<h1>JSports System/Users Logs</h1>
<form action="#<?php //echo Route::_('index.php?option=com_jsports&view=venues'); ?>" method="post" name="adminForm" id="adminForm">
	<?php  echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	
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
					<th scope="col" class="w-15">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_LOGDATE', 'a.logdate', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-10">
						<?php echo HTMLHelper::_('searchtools.sort', 'COM_JSPORTS_NAME', 'a.username', $listDirn, $listOrder); ?>
					</th>
					<th scope="col" class="w-70">
						<?php echo Text::_('COM_JSPORTS_LOGMSG'); ?>
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
						<?php echo $item->logdate; ?>
					</th>
					<td class="">
						<?php echo $item->username; ?>
					</td>
					<td class="">
						<?php echo $item->msg; ?>
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