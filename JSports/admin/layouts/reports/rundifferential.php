
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="report-table-wrap mt-3">
<table class="table table-striped table-hover table-sm report-table">

<thead>
<tr>
  <th>DivID</th>
  <th>Division Name</th>
  <th>Game Count</th>
  <th>Differential</th>
</tr>
</thead>

<tbody>
<?php
foreach ($displayData as $row) {
    ?>
    <tr>
					<td><?php echo $row->divisionid; ?> </td>
					<td><?php echo $row->name; ?> </td>
					<td><?php echo $row->gamecount; ?> </td>
					<td><?php echo $row->diff; ?> </td>
				</tr>
<?php
}
?>
</tbody>
</table>
</div>

