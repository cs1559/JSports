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

$input = Factory::getApplication()->input;

$regid = $input->get('id',0);


?>

<h1>Registration Confirmation</h1>

<p>
Thank you for registering for an upcoming season with SWIBL.  Your registration ID is <strong> <?php echo $regid; ?></strong>. 
You should be receiving a confirmation email to the email address provided on the registration form.
</p>
<p>
If you have any questions or do not receive the confirmation email, please email SWIBL by sending an email to 
info@swibl.org.
</p>

