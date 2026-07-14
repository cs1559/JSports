<?php 
/**
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2 or later, see http://www.gnu.org/licenses/gpl-2.0.html
 * @todo        Need to see if this is needed.
 */

?>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="boxchecked" value="0"/>
<?php // echo HTMLHelper::_('form.token'); ?>

<h2> <?php echo $this->form->renderField('title'); ?></h2>
<h3><?php //echo $this->version; ?></h3>