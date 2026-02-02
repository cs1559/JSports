<?php
/**
 * @package     Postings.Site
 * @subpackage  com_jsports
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/*
 * CHANGE HISTORY:
 * 2024-03-01  Removed/commented the sortable column headers in the table for division name and city.
 */


defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$editIcon = '<span class="fa fa-pen-square me-2" aria-hidden="true"></span>';

$clientId  = (int) $this->state->get('client_id', 0);

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.postings.style');

?>

	<h2>Postings <?php //echo $this->program->name; ?></h2>

<form action="#<?php //echo Route::_('index.php?option=com_jsports&view=teams'); ?>" method="post" name="adminForm" id="adminForm">
	
	<div class="row">
		<div class="col-sm-3">
			<?php echo $this->filterForm->renderField('bulletintype', 'filter'); ?>
		</div>
		<div class="col-sm-3">
			<?php echo $this->filterForm->renderField('search', 'filter'); ?>
		</div>


		<?php echo $this->filterForm->renderField('limit', 'list'); ?>	
	
	</div>

<?php 
    if ($this->isProgramPending) {
        ?>
        <br/>
        <div class="alert alert-warning" role="alert">
            <strong><?php echo Text::_('COM_JSPORTS_PROGRAM_PENDING'); ?></strong>
        </div>
        <?php 
    }
?>
<div class="container">

	
	<div class="jsports-table-wrapper">
	<table class="table">
  	<thead>
    	<tr>
      		<th class="w-5" scope="col">Date</th>
      		<th class="w-40" scope="col">Title</th>
      		<!--  <th class="w-30" scope="col">Type</th>   -->
      		<th class="w-10" scope="col">Attachment</th>
    	</tr>
  	</thead>

	<?php foreach ($this->items as $item) {	?>
		<tr>
			<?php 
			 $bdate = (new DateTime($item->createdate))->format('Y-m-d');
			?>
			<th scope="row"><?php echo $bdate; ?></th>
			<td><?php echo strtoupper($item->title); ?>
<a class="link-primary" style="color: red !important;"
   href="#details-<?php echo (int) $item->id; ?>"
   data-bs-toggle="collapse"
   role="button"
   aria-expanded="false"
   aria-controls="details-<?php echo (int) $item->id; ?>">
   [ Show ]
</a>
			</td>
			<!-- <td><?php echo $item->bulletintype; ?></td>   -->
			<td>
				<?php 
					if (!empty($item->attachment)) {
					 //   echo substr($item->attachment,0,10) . "...";
					    echo "<a href='" . JSHelper::getBulletinAttachmentURL($item->id, $item->attachment)
					          . "'  target='_blank'>Click Here </a>";
					}?>
			</td>
		</tr>
		
		      <!-- Hidden "details" row -->
        <tr class="collapse" id="details-<?php echo $item->id;?>">
            <td colspan="5">
              <div class="p-3 bg-light border rounded">
                <h3><?php echo $item->title; ?></h3>
           		<?php echo $item->content; ?>
                
              </div>
            </td>
        </tr>
	<?php } ?>

	</table>
	</div>
</div>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>