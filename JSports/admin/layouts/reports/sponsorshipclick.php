<?php
use FP4P\Component\JSports\Site\Helpers\JSHelper;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<div class="report-table-wrap mt-3">
<table class="table table-striped table-hover table-sm report-table">

<thead>
<tr>
  <th>Name</th>
  <th>Sponsorship Level</th>
  <th>Start Date</th>
  <th>End Date</th>
  <th>Impressions</th>
  <th>Clicks</th>
</tr>
</thead>

<tbody>
<?php
foreach ($displayData as $row) {
    ?>
			<tr>
				<td><?php echo $row->sponsor_name; ?></td>
				<td><?php echo $row->plan_name; ?>
				<td><?php echo $row->startdate; ?> </td>
				<td><?php echo $row->enddate; ?> </td>
				<td><?php echo $row->impressions; ?> </td>
				<td><?php echo $row->clicks; ?> </td>
				
			</tr>
			<?php
}
?>
</tbody>
</table>
</div>

