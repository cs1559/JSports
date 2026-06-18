<?php
/**
 * @version     0.0.1
 * @package     JSports
 * @subpackage  Bulletin.Site
 * @category    Template
 * @desc        Template to create/edit a bulletin
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

use FP4P\Component\JSports\Administrator\Helpers\Html;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useStyle('com_jsports.jsports.style');
$wa->useStyle('com_jsports.game.style');

$token = Session::getFormToken();

?>

<h1>Add/Edit Bulletin </h1>
<?php //if ($this->item->id > 0) {?>
<p><strong>NOTE:</strong>  You need to create the bulletin FIRST before you can add an attachment.</p>
<?php //} ?>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=bulletin&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="bulletinForm" id="bulletin-form" class="form-validate">

		<?php echo $this->form->renderField('category'); ?>
		<?php echo $this->form->renderField('title'); ?>
		<?php echo $this->form->renderField('content'); ?>
		<br/>
		<?php //echo $this->form->renderField('location'); ?>
		<?php //echo $this->form->renderField('startdate'); ?>
		<?php //echo $this->form->renderField('enddate'); ?>
		<?php //echo $this->form->renderField('externalurl'); ?>

		<?php echo $this->form->renderField('teamid'); ?>
		<?php echo $this->form->renderField('id'); ?>

		<?php if ($this->item->id > 0) {?>		
		<?php if ($this->attachmentsenabled) { ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('attachment'); ?>
			</div>
            <div class="controls">
            <?php if ($this->item->hasAttachment) { ?>
                <a href="<?php echo $this->item->attachmentUrl;?>" target="_blank"><?php echo $this->item->attachment; ?></a>
<a href="<?php echo Route::_('index.php?option=com_jsports&task=bulletin.deleteAttachment&id=' .
    (int) $this->item->id . '&' . $token . '=1&teamid=' . $this->item->teamid); ?>"
   class="text-danger"
   title="Remove Attachment"
   aria-label="Remove Attachment">
   <span class="icon-delete" aria-hidden="true"></span>
</a>
            	<?php } else {
            	   echo "No attachments";
            	}?>					
            </div>
		</div>
		<?php echo $this->form->renderField('afile'); ?>
		<?php } // end of if attachments enabled ?>
		<?php } // if item->id > 0 ?>
		
	<br/><br/>
	<input type="hidden" name="task" value="">

	<?php echo HTMLHelper::_('form.token'); ?>
	
	<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="bulletin.save">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="bulletin.cancel" formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
	</div>
	
	<?php echo HTMLHelper::_('form.token'); ?>
	
</form>