<?php
/**
 * @version     0.0.1
 * @package     JSports
 * @subpackage  Teams.Site
 * @category    Template
 * @desc        Template for uploading a team logo.
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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Set Page Title and load specific stylesheet
// $document = Factory::getDocument();
$document = Factory::getApplication()->getDocument();

// Load
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.teamprofile.style');

$document->setTitle(Text::_('COM_JSPORTS_TEAMPROFILE_PAGE_TITLE'));
?>

<form  enctype="multipart/form-data" action="<?php
        echo Route::_('index.php?option=com_jsports&view=logoupload'); ?>"
		method="post" name="uploadform" id="upload-form" class="form-validate">
	

<div class="teamprofile-header-container" >
	<div class="teamprofile-header-name">
		<h1><?php echo $this->teamname;?> Logo Upload</h1>
	</div>
</div>

<hr class="hr-bar">

<?php echo $this->form->renderField('id');?>
<?php echo $this->form->renderField('uploadfile');?>

	
	<input type="hidden" name="task" value="">
<?php echo HTMLHelper::_('form.token'); ?>

<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="logoupload.save">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="logoupload.cancel"  formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
</div>


</form>