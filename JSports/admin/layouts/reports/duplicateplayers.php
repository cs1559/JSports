
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<div class="report-table-wrap mt-3">
<table class="table table-striped table-hover table-sm report-table">

<thead>
<tr>
  <th>Last Name</th>
  <th>First Name</th>
  <th>Soundex Value</th>
  <th>Age Group</th>
  <th>Division</th>
  <th>Team Name</th>
</tr>
</thead>

<tbody>
<?php
foreach ($displayData as $row) {
    ?>
    <tr>
					<td><?php echo $row->lastname; ?> </td>
					<td><?php echo $row->firstname; ?> </td>
					<td><?php echo $row->soundex; ?> </td>
					<td><?php echo $row->agegroup; ?> </td>
					<td><?php echo $row->divisionname; ?></td>
					<td><?php echo $row->teamname; ?> </td>

				</tr>
<?php
}
?>
</tbody>
</table>
</div>

