<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     0.0.1
 * @package     JSports
 * @subpackage  Rosters.Site
 * @category    Templates
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * 
 * CHANGES:
 * 2024-02-02   Removed ID from the display list.
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$clientId  = (int) $this->state->get('client_id', 0);
$user      = Factory::getUser();

// Set Page Title and load specific stylesheet
$document = Factory::getDocument();

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.jsports.style');
$wa->useStyle('com_jsports.teamprofile.style');

$document->setTitle(Text::_('COM_JSPORTS_TEAMROSTER_PAGE_TITLE'));
$canEdit = true;

?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=rosters'); ?>" method="post" name="adminForm" id="adminForm">
	
	<div class="teamprofile-header-container" >
	<div class="teamprofile-header-name">
		<h1>
		<a href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $this->team->id); ?>">
				<?php echo $this->team->name; ?></a> - Manage Roster
		</h1>
	</div>
</div>
					<?php 
					   if ($this->canEdit) {
					?>
						<a class="btn btn-primary btn-sm" 
							href="<?php echo Route::_('index.php?option=com_jsports&view=roster&layout=edit'
							    . '&teamid=' . $this->team->id . '&programid=' . $this->program->id . '&id=0' ); ?>"><?php echo Text::_('COM_JSPORTS_ROSTER_ADD_BUTTON'); ?> </a>
					<?php 
					   } 
					?>
<a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $this->team->id); ?>">Team Profile </a>
<?php echo $this->program->name . " (" . $this->program->status . ")"; ?>
<hr class="hr-bar">
	
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		
	<?php else : ?>
		<div class="jsports-table-wrapper">
		<table class="table" id="rostersList">
			<thead>
				<tr>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_LASTNAME'); ?>
					</th>
					<th scope="col" class="w-10">
						<?php echo Text::_('COM_JSPORTS_FIRSTNAME'); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_CLASSIFICATION'); ?>
					</th>					
					<?php if ($this->canEdit) {?>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_ACTIONS'); ?>
					</th>				
					<?php } ?>						
				</tr>
			</thead>
			<tbody>
			<?php
			
			$n = count($this->items);
			foreach ($this->items as $i => $item) :
				?>
				<tr class="row<?php echo $i % 2; ?>">
<?php 
//     $canChange  = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin; 
//                    $canChange  = $user->authorise('core.edit.state', 'com_jsports.rosters.' . $item->id);

?>

					<th scope="row" class="has-context">
					<?php 
				       echo $this->escape($item->lastname);
					?>
					
					</th>
					<td class="">
						<?php echo $item->firstname; ?>
					</td>
					<td class="">
						<?php echo $item->classification; ?>
					</td>
					
				<?php if ($this->canEdit) {?>	
					<td class="">
						<a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_jsports&view=roster&layout=edit&id=' . $item->id); ?>">Edit</a>
						<a class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');" href="<?php echo Route::_('index.php?option=com_jsports&task=roster.delete&id=' . $item->id); ?>">Delete</a>
					</td>				
				<?php } ?>	
					
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