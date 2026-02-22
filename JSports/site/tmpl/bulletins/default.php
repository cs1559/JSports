<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     1.0.0
 * @package     JSports
 * @subpackage  Schedules.Site
 * @category    Templates
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use Joomla\CMS\Session\Session;

$clientId  = (int) $this->state->get('client_id', 0);
// $user      = Factory::getUser();
$user = UserService::getUser();


// Set Page Title and load specific stylesheet
// $document = Factory::getDocument();
$document = Factory::getApplication()->getDocument();

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.jsports.style');
$wa->useStyle('com_jsports.teamprofile.style');

$document->setTitle(Text::_('COM_JSPORTS_TEAMBULLETINS_PAGE_TITLE'));

$token = Session::getFormToken();

?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=bulletins'); ?>" method="post" name="adminForm" id="adminForm">
	
	<div class="" >
	<div class="teamprofile-header-name">
		<h1>
			<?php echo Text::_('COM_JSPORTS_BULLETINS_HEADING'); ?>
		</h1>
	</div>
</div>
		<?php 
		   if ($this->canEdit) {
		?>
			<a class="btn btn-primary btn-sm"
				href="<?php echo Route::_('index.php?option=com_jsports&view=bulletin&layout=edit&id=0' ); ?>">Add Bulletin 
			</a>

		<?php 
		   } 
		?>
                    <hr class="hr-bar">
	
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('COM_JSPORTS_NO_RECORDS_FOUND'); ?>
		</div>
		
	<?php else : ?>
		<div class="jsports-table-wrapper">
		<table class="table" id="gamelist">
			<thead>
				<tr>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_CREATEDATE'); ?>
					</th>
					<th scope="col" class="w-10">
						<?php echo Text::_('COM_JSPORTS_TITLE'); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_APPROVED'); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_ATTACHMENT'); ?>
					</th>
					<th scope="col" class="w-15">
						<?php echo Text::_('COM_JSPORTS_ID'); ?>
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
						<th scope="row" class="has-context">

					<?php 
				       echo $item->createdate;
					?>
					</td>

					<td class="">
						<?php echo $item->title; ?>
					</td>
					
					<td class="">
						<?php 
					          echo JSHelper::translateYesNo($item->approved);
				           ?>
					</td>
					<td class="">
						<?php 
						if (!empty($item->attachment)) {
						 //   echo substr($item->attachment,0,10) . "...";
						    echo "<a href='" . JSHelper::getBulletinAttachmentURL($item->id, $item->attachment)
						          . "'  target='_blank'>Click Here </a>";
						}?>
					</td>					
					<td class="">
						<?php echo $item->id; ?>
					</td>
					
					<td class="">
    					<?php 
    					//     canEditGame(team id of schedule being viewed, game object)
    					if ($this->canEdit) {
    					 ?>
        							<a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_jsports&view=bulletin&layout=edit&id=' . $item->id); ?>">Edit</a>
        							<a class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');" href="<?php echo Route::_('index.php?option=com_jsports&task=bulletin.delete&id=' . $item->id . '&' . $token . '=1'); ?>">Delete</a>
						  <?php 
    					 } 
    						?>
					</td>
					
					
				</tr>
				<?php endforeach; ?>	
			</tbody>
		</table>
		<span class="game-legend"><?php echo Text::_('COM_JSPORTS_BULLETIN_APPROVAL_NOTE'); ?></span>
		<br/>
		<?php // load the pagination. ?>
		<?php echo $this->pagination->getListFooter(); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>