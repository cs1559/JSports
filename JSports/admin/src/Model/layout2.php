<?php
defined('_JEXEC') or die();

use Joomla\CMS\Router\Route;

?>

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link
	href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
	rel="stylesheet">

<div class="mod_jsports_module_container modern">

	<div class="latest-header">
		<div>
			<h2 class="mb-0"><?php echo $heading; ?></h2>
			<small class="" style="color: #ffffff;">Latest League
				Bulletins</small>
		</div>

		<a href="<?php echo Route::_('index.php?option=com_jsports&view=postings'); ?>" class="btn btn-sm btn-outline-primary"><?php echo $buttontext; ?></a>
	</div>

		<div class="latestbulletins-table-wrapper">
			<table class="table table-modern align-top mb-0">
				<thead>
					<tr>
						<th>Title</th>
						<th>Date</th>
						<th>Contents</th>
					</tr>
				</thead>

				<tbody>		

<?php
if (count($rows)) {
    foreach ($rows as $row) {

        $date = new DateTime($row->createdate);
        $newdate = $date->format('m-d-Y');
        $owner = empty($row->owner_username) ? 'Unknown' : $row->owner_username;

    ?>
    <tr>
			<td class="fw-semibold"><?php echo strtoupper($row->title); ?></td>
			<td class="text-muted small"><?php echo $newdate;?> </td>
			<td><?php echo rtrim(substr($row->content,0,$contentlength));?></td>
	</tr>
	<?php
    } 
} else { ?>
    <tr><td colspan="5">No bulletins found</td></tr>
    <?php 
}
?>

</tbody>
</table>

</div>