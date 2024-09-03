<?php
/**
 * @package     Registers.Site
 * @subpackage  com_mywalks
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

?>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=registration&layout=edit&Itemid=' . $itemid); ?>"
	method="post" name="registerform" id="register-form" class="form-validate">

<h1>Online Registration - SELECT TEAM</h1>

<strong>Please select the program you are wanting to register for:</strong>
<br/><br/>
<?php echo $this->form->renderField('programid');?>

<?php echo HTMLHelper::_('form.token'); ?>

<div class="control-group">
    <div class="controls">
        <button type="submit" class="btn btn-primary validate" name="task" value="registration.edit">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_('Continue'); ?>
        </button>
        <button type="submit" class="btn btn-danger" name="task" value="registration.cancel" formnovalidate>
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo Text::_('JCANCEL'); ?>
        </button>
        <input type="hidden" name="option" value="com_jsports">
    </div>
</div>


</form>