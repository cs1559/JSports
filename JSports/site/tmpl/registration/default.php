<?php
/**
 * @package     Registration.Administrator
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

$document = Factory::getDocument();
$document->addStyleSheet(JUri::base() . 'components\com_jsports\assets\css\registration.css');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>

<h1>Registration Edit</h1>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=registrations&layout=edit'); ?>"
	method="post" name="registerform" id="register-form" class="form-validate">
	
<div class="row">

<?php echo $this->program->name; ?>
<?php echo $this->form->renderField('id');?>
<?php echo $this->form->renderField('programid');?>
<?php echo $this->form->renderField('teamname');?>
<?php echo $this->form->renderField('name');?>
<?php echo $this->form->renderField('email');?>
<?php echo $this->form->renderField('phone');?>
<?php echo $this->form->renderField('address');?>
<?php echo $this->form->renderField('city');?>
<?php echo $this->form->renderField('state');?>
<?php 

if ($this->options->includegroups) {
    echo $this->form->renderField('grouping');
}
    
?>
<?php 
if ($this->options->includeskills) {
    echo $this->form->renderField('skilllevel');
}
?>
<?php 
if ($this->options->includeagreement) {
?>
<div class="row agreement-container">
<h3>COACHES AGREEMENT</h3>
All coaches are required to review the Coaches Agreement/Code of Conduct before submitting their registration.  
Please <a href="<?php echo $this->agreementurl;?>" target="_blank">CLICK HERE TO DOWNLOAD AGREEMENT</a> and review the document before continuing.   
<?php echo $this->form->renderField('tosack');?>
</div>

<?php 
}
?>
<?php 
if ($this->options->includeregisteredby) {
    echo $this->form->renderField('registeredby');
}
?>
<?php echo $this->form->renderField('captcha');?>

</div>


<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="registration.save">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="registration.cancel" formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
</div>

<?php echo HTMLHelper::_('form.token'); ?>
</form>


