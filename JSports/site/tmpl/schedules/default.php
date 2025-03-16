<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     1.0.0
 * @package     JSports
 * @subpackage  Schedules.Site
 * @category    Templates
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 *
 * CHANGES:
 * 2024-02-02   Removed ID from the display list.
 * 2024-02-13   Remove d W/L and score info.  replaced with location and time.
 *      - Reversed "Home @ Away" to "Away @ Home"
 * 2024-02-22   Changed logic on when to display the EDIT/DELETE buttons.
 * 2024-03-01   Added DIV with jsports-table-wrapper to make table responsive.
 * 2025-01-28   Use SITE SecurityService instead of Admin
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;

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

$document->setTitle(Text::_('COM_JSPORTS_TEAMSCHEDULE_PAGE_TITLE'));

?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=schedules'); ?>" method="post" name="adminForm" id="adminForm">
	
	<div class="" >
	<div class="teamprofile-header-name">
		<h1>		
			<a href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $this->team->id); ?>">
				<?php echo $this->team->name; ?></a> - Manage Schedule
		</h1>
	</div>
</div>
					<?php 
					   if ($this->canEdit) {
					?>
						<a class="btn btn-primary btn-sm" 
							href="<?php echo Route::_('index.php?option=com_jsports&view=game&layout=edit'
							    . '&teamid=' . $this->team->id . '&programid=' . $this->program->id . '&id=0' ); ?>">Add Game </a>

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
			<?php echo Text::_('COM_JSPORTS_NO_RECORDS_FOUND'); ?>
		</div>
		
	<?php else : ?>
		<div class="jsports-table-wrapper">
		<table class="table" id="gameList">
			<thead>
				<tr>
					<th scope="col" class="w-10">
						<?php echo Text::_('COM_JSPORTS_GAMEDATE'); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_NAME'); ?> (Away @ Home)
					</th>								
					<th scope="col" class="w-5">
						<?php echo Text::_('COM_JSPORTS_TIME'); ?>
					</th>				
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_STATUS'); ?>*
					</th>				
					
					<th scope="col" class="w-5 d-none d-md-table-cell">
						<?php // echo Text::_('JGRID_HEADING_ID');  ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			
			$n = count($this->items);
			foreach ($this->items as $i => $item) :
			
			     if (!$item->leaguegame) {
			         $gameclass="nonleague-game";
			     } else {
			         $gameclass="league-game";        
		         }
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<th scope="row" class="has-context">
					<?php 
				       echo $this->escape($item->gamedate);
					?>
					
					</th>
					<td class="<?php echo $gameclass; ?>">
						<?php echo $item->name; ?><br/>
						<span style="font-style: italic"><?php echo $item->location; ?></span>
					</td>
					
					<td class="">
						<?php 
					          echo JSHelper::displayGameTime($item->gametime);
				           ?>
					</td>
					<td class="">
						<?php echo $item->gamestatus; ?>
					</td>
					<td class="">
							<?php // echo $item->id; ?>
    					<?php 
    					//     canEditGame(team id of schedule being viewed, game object)
    					if ($this->canEdit && (SecurityService::canEditGame($this->team->id, $item))) {
    					//if ($this->canEdit) {
    					    // Remove all buttons if the game has been completed.
        					    if ($item->gamestatus != 'C') {
        					    ?>
        							<a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_jsports&view=game&layout=edit&id=' . $item->id . '&contextid=' . $this->team->id); ?>">Edit</a>
        							<a class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');" href="<?php echo Route::_('index.php?option=com_jsports&task=game.delete&id=' . $item->id . '&contextid=' . $this->team->id); ?>">Delete</a>
        						  <?php 
        					    }
    					 } 

    			         if (SecurityService::isAdmin() && $item->gamestatus == "C") {
    				          ?>
    				        <a class="btn btn-secondary btn-sm" onClick="return confirm('Are you sure?');" href="<?php echo Route::_('index.php?option=com_jsports&task=game.reset&id=' . $item->id); ?>">Reset Status</a>
    				          <?php
    				     }
    						?>
					</td>
				</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
		<span class="game-legend"><?php echo Text::_('COM_JSPORTS_GAME_LEGEND'); ?></span>
		<br/>
		<?php // load the pagination. ?>
		<?php echo $this->pagination->getListFooter(); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>