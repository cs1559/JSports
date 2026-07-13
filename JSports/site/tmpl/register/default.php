<?php
/**
 * @package     Registers.Site
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
use Joomla\CMS\Component\ComponentHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$params = ComponentHelper::getParams('com_contact');
$itemid = $params->get('itemid');

//echo Route::_('index.php?option=com_jsports&view=registration&Itemid=' . $itemid

?>

<?php
$ajaxUrl = Route::_('index.php?option=com_jsports&task=registration.checkteamauth', false);
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('register-form');
    var submitBtn = form.querySelector('button[name="task"][value="registration.edit"]');

    submitBtn.addEventListener('click', function (e) {
        // Only intercept the "Continue" button, not Cancel
        e.preventDefault();

        // Grab the checked radio value for returningteam
        var returningTeamRadio = form.querySelector('input[name="jform[returningteam]"]:checked');
        var returningTeamValue = returningTeamRadio ? returningTeamRadio.value : null;
    
        if (returningTeamValue === 'NO') {
            // Clear out fields not needed for a new team registration
            var usernameField = form.querySelector('input[name="jform[username]"]');
            var passwordField = form.querySelector('input[name="jform[password]"]');
            var teamidField   = form.querySelector('input[name="jform[teamid]"], select[name="jform[teamid]"]');
        
            if (usernameField) usernameField.value = '';
            if (passwordField) passwordField.value = '';
            if (teamidField)   teamidField.value = 0;
    
            // Skip the AJAX auth check entirely, just submit
            form.submit();
            return;
        }
    
        submitBtn.disabled = true;

        var formData = new FormData(form);

        fetch('<?php echo $ajaxUrl; ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (response) { return response.json(); })
        .then(function (result) {
            submitBtn.disabled = false;

            if (result.success) {
                // auth passed - remove our listener flag and actually submit
                form.submit();
            } else {
                alert(result.message || 'Authentication failed.');
            }
        })
        .catch(function (err) {
            submitBtn.disabled = false;
            alert('An error occurred while validating your login. Please try again.');
            console.error(err);
        });
    });
});
</script>

<form action="<?php echo Route::_('index.php?option=com_jsports&task=register.save&Itemid=' . $itemid); ?>"
	method="post" name="registerform" id="register-form" class="form-validate">

<h1>Online Registration</h1>

<strong>Please select the program you are wanting to register for:</strong>
<br/>
NOTE:  If you are a returning team, you will need to provide a username/password in order to verify that you are permitted to register
that team.
<br/>
<?php echo $this->form->renderField('programid');?>
<?php echo $this->form->renderField('returningteam');?>
<?php echo $this->form->renderField('lastprogramplayed');?>
<?php echo $this->form->renderField('teamid');?>
<?php echo $this->form->renderField('username');?>
<?php echo $this->form->renderField('password');?>

<?php echo HTMLHelper::_('form.token'); ?>

<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="registration.edit">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('Continue'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="register.cancel" formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
</div>


</form>