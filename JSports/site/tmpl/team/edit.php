<?php
/**
 * @package     Team.Administrator
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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>
<h1>Edit Profile -<?php echo $this->data->name; ?>  </h1>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=team&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="teamForm" id="team-form" class="form-validate">

		<?php echo $this->form->renderField('name'); ?>
		<?php echo $this->form->renderField('contactname'); ?>
		<?php echo $this->form->renderField('contactemail'); ?>
		<?php echo $this->form->renderField('contactphone'); ?>
		<?php echo $this->form->renderField('city'); ?>
		<?php echo $this->form->renderField('state'); ?>
		<?php echo $this->form->renderField('websiteurl'); ?>							
		<?php echo $this->form->renderField('id'); ?>
		<?php echo $this->form->renderField('ownerid'); ?>
		
		
		<?php 
		  if ($this->attributesenabled) {
		      if ($this->canEditAttributes) { ?>
    			<h4>Profile Attributes</h4>
    			<hr/>
		<?php
		      $newLabel = Text::sprintf('COM_JSPORTS_TOURNAMENT', $this->recentprogram->name);
		      $this->form->setFieldAttribute('tournament', 'label', $newLabel);
		      if ($this->canEditTournamentFlag) {
		          $this->form->setFieldAttribute('tournament', 'disabled', 'false');
		      } else {
		          $this->form->setFieldAttribute('tournament', 'disabled', 'true');
		      }
		      
		      if ($this->canEditTournamentFlag) {
		          echo $this->form->renderField('tournament'); 
		      }
		?>
		<?php echo $this->form->renderField('openroster'); ?>
		<?php echo $this->form->renderField('showcontactinfo'); ?>
		<?php 
                }
            }
		?>
	<input type="hidden" name="origowner" value="<?php echo $this->item->ownerid; ?>">
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
	
	<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="team.save">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="team.cancel" formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
</div>
	
	
</form>