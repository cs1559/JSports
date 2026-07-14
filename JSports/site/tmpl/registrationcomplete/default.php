<?php
/**
 * @package     JSports.Site
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Helpers\RegistrationHelper;

?>

<h1>Registration Confirmation</h1>

<p>
Thank you for registering for an upcoming season with SWIBL.  Your registration ID is <strong> <?php echo $this->data->id; ?></strong>. 
You should be receiving a confirmation email to the email address provided on the registration form.
</p>
<p>
<h3>Registration Information</h3>
<table style="width: 80%;">
<tbody>
	<tr>	<td>ID:</td><td><?php echo $this->data->id; ?></td></tr>
		<tr>	<td>Program:</td><td><?php echo $this->programname; ?></td></tr>
		<tr>	<td>Team Id:</td><td><?php echo $this->data->teamid; ?></td></tr>
		<tr>	<td>Team Name:</td><td><?php echo $this->data->teamname; ?></td></tr>
		<tr>	<td>Coach/Contact Name:</td><td><?php echo $this->data->name; ?></td></tr>
		<tr>	<td>Email:</td><td><?php echo $this->data->email; ?></td></tr>
		<tr>	<td>City:</td><td><?php echo $this->data->city; ?></td></tr>
		<tr>	<td>State:</td><td><?php echo $this->data->state; ?></td></tr>
		<tr>	<td>Phone:</td><td><?php echo $this->data->phone; ?></td></tr>
		<tr>	<td>Grouping:</td><td><?php echo $this->data->grouping; ?></td></tr>
		<tr>	<td>Level:</td><td><?php echo RegistrationHelper::translateSkillLevel($this->data->skilllevel); ?></td></tr>
		<tr>	<td>Playoffs:</td><td><?php echo JSHelper::translateYesNo($this->data->playoffs); ?></td></tr>
		<tr>	<td>Team Id:</td><td><?php echo $this->data->teamid; ?></td></tr>
		<tr>	<td>Registered By:</td><td><?php echo RegistrationHelper::translateOptionalValue($this->data->registeredby); ?></td></tr>
		<tr>	<td>Registered Date:</td><td><?php echo RegistrationHelper::translateOptionalValue($this->data->regdate); ?></td></tr>
	</tr>
</tbody>
</table>
</p>
<p>
If you have any questions or do not receive the confirmation email, please email SWIBL by sending an email to 
info@swibl.org.
</p>

