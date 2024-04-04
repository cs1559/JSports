<?php
/**
 * @package     Leagues.Administrator
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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

?>

<form action="<?php echo Route::_('index.php?option=com_jsports&view=league&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="programs-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

    		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_DETAILS')); ?>
    		<div class="row">
    			<div class="col-md-9">
    				<div class="row">
    					<div class="col-md-8">
    						<?php echo $this->form->renderField('description'); ?>
    					</div>
    				</div>
    			</div>
    			<div class="col-md-3">
    				<div class="card card-light">
    					<div class="card-body">
    						<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
    					</div>
    				</div>
    			</div>
    		</div>
    		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>