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

<h1><?php echo Text::_('COM_JSPORTS_REGISTRATION_PAGE_HEADER'); ?></h1>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=registrations&layout=edit'); ?>"
	method="post" name="registerform" id="register-form" class="form-validate">
	
<?php if (!is_null($this->program->registrationnotes)) {?>	
<div>
	<p>
		<?php echo $this->program->registrationnotes; ?>	
	</p>
</div>	
<?php } ?>

	
<div class="row">
<?php echo $this->form->renderField('id');?>
<?php echo $this->form->renderField('programid');?>
<?php echo $this->form->renderField('teamname');?>
<?php echo $this->form->renderField('name');?>
<?php echo $this->form->renderField('email');?>
<?php echo $this->form->renderField('phone');?>
<?php echo $this->form->renderField('address');?>
<?php echo $this->form->renderField('city');?>
<?php echo $this->form->renderField('state');?>
<?php echo $this->form->renderField('existingteam');?>
<?php echo $this->form->renderField('grouping');?>
<?php echo $this->form->renderField('skilllevel');?>
<?php echo $this->form->renderField('playoffs');?>
<div class="row agreement-container">
<h3>COACHES AGREEMENT</h3>
All coaches are required to review the Coaches Agreement/Code of Conduct before submitting their registration.  
Please download and review the document by clicking on the link below.  You must check the box below before you can complete 
your registration.
<a href="<?php echo $this->agreementurl;?>" target="_blank">CLICK HERE TO DOWNLOAD AGREEMENT</a>   
<strong><?php echo $this->form->renderField('tosack');?></strong>
</div>
<?php echo $this->form->renderField('registeredby');?>
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

Template:  swiblspring.php <br/>

