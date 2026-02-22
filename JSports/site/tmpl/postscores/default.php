<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     0.0.1
 * @package     JSports
 * @subpackage  PostScores.Site
 * @category    Templates
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
use FP4P\Component\JSports\Site\Services\SecurityService;
use FP4P\Component\JSports\Site\Services\UserService;
use FP4P\Component\JSports\Site\Helpers\JSHelper;

// $clientId  = (int) $this->state->get('client_id', 0);
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

$document->setTitle(Text::_('COM_JSPORTS_POSTSCORES_PAGE_TITLE'));

?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=schedules'); ?>" method="post" name="adminForm" id="adminForm">
	
	<div class="" >
	<div class="teamprofile-header-name">
		<h1><a href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $this->team->id);?>"><?php echo $this->team->name; ?></a> - Post Scores</h1>
	</div>
</div>
<a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $this->team->id); ?>">Team Profile </a>					
<?php echo $this->program->name . " (" . $this->program->status . ")"; ?>
<hr class="hr-bar">
	
	<?php if ($this->program->status == 'C') { ?>
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('Scores can no longer be posted - Program is closed'); ?>
		</div>

	<?php } ?>
	
	<?php if (empty($this->items)) : 
	       ?>
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('COM_JSPORTS_NO_RECORDS_FOUND'); ?>
		</div>
		
	<?php else : ?>
		<div class="container">
		<?php 
	       	$n = count($this->items);
			foreach ($this->items as $i => $item) :
			?>		
           		 <div class="row" style="border-style: solid; margin-bottom: 15px; padding-top: 10px; padding-bottom: 10px;">
                    <div class="col-sm-3">
                      <strong><?php echo Text::_('COM_JSPORTS_GAMEDATE') . ":</strong> " . $item->gamedate . " (" . JSHelper::displayGameTime($item->gametime) . ")"?>
                    </div>
                    <div class="col-sm-5">
                      <?php echo $item->name;?>
                    </div>
                    <div class="col-sm-2">
			<?php echo JSHelper::translateGameStatus($item->gamestatus); ?>
                    </div>

                    <div class="col-sm">
					<?php 
				//if ($this->canEdit && (SecurityService::canEditGame($this->team->id, $item))) {

				if ($this->canEdit && $this->program->status == 'A') {
?>	
						<a class="btn btn-primary btn-sm" href="<?php echo Route::_('index.php?option=com_jsports&view=postscore&id=' . $item->id . '&teamid=' . $this->team->id); ?>">Post Score</a>

					</td>				
				<?php } ?>						
                    </div>
  				</div>
		<?php endforeach; ?>	
		</div>
	
	<?php endif; ?>
	
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>