<?php
/**
 * @package     PostScore.Site
 * @subpackage  com_jsports
 * 
 * This template provides a user the ability to EDIT a team record from the website's FRONT END (site).  Most changes to a 
 * team should be done on the administrator site.
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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
// Set Page Title and load specific stylesheet
$document = Factory::getDocument();
$document->setTitle(Text::_('COM_JSPORTS_POSTSCORE_PAGE_TITLE'));
?>

<h1><?php echo Text::_('COM_JSPORTS_POSTSCORE_PAGE_TITLE'); ?></h1>
<form action="<?php echo Route::_('index.php'); ?>"
	method="post" name="postscoreForm" id="postscore-form" class="form-validate">

	<?php // echo "Home: " . Html::getHomeTeamlist($this->teamid, $this->programid); ?>
	<?php // echo "Away: " . Html::getAwatTeamlist($this->teamid, $this->programid); ?>
	
	<?php echo $this->form->renderField('name'); ?>
	<?php echo $this->form->renderField('hometeamid'); ?>
	<?php echo $this->form->renderField('hometeamname'); ?>
	<?php echo $this->form->renderField('hometeamscore'); ?>	
	<?php echo $this->form->renderField('awayteamname'); ?>
	<?php echo $this->form->renderField('awayteamid'); ?>	
	<?php echo $this->form->renderField('awayteamscore'); ?>
	<?php echo $this->form->renderField('teamid');?>
	<?php echo $this->form->renderField('programid');?>
	<?php echo $this->form->renderField('redirectteamid');?>
					
	<?php echo $this->form->renderField('id'); ?>
	<?php echo $this->form->renderField('gamedate'); ?>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
	
	<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="postscore.save2">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="postscore.cancel" formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
</div>
	
	
</form>