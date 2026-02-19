
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.report-table-wrap{
  /* max-height: 400px; */
  overflow: auto;
  border: 1px solid #dee2e6;
  border-radius: .5rem;
  background: #fff;
}

.report-table{
  min-width: 1200px;
  margin-bottom: 0;
}

.report-table thead th{
  position: sticky;
  top: 0;
  background: #f8f9fa;
  z-index: 2;
  white-space: nowrap;
}

.report-table th,
.report-table td{
  white-space: nowrap;
  vertical-align: top;
  padding: .35rem .5rem;
  font-size: .9rem;
}
</style>

<div class="report-table-wrap mt-3">
<table class="table table-striped table-hover table-sm report-table">

<thead>
<tr>
  <th>Team</th>
  <th>Grouping</th>
  <th>Coach</th>
  <th>Address</th>
  <th>City</th>
  <th>Email</th>
  <th>Phone</th>
  <th>Cell</th>
  <th>Registered By</th>
  <th>Skill</th>
  <th>Playoffs</th>
</tr>
</thead>

<tbody>
<?php
foreach ($displayData as $row) {
    ?>
    <tr>
					<td><?php echo $row->teamname; ?></td>
					<td><?php echo $row->grouping; ?> </td>
					<td><?php echo $row->coach_name; ?> </td>
					<td><?php echo $row->address; ?> </td>
					<td><?php echo $row->city; ?> </td>
					<td><?php echo $row->email; ?> </td>
					<td><?php echo $row->phone; ?> </td>
					<td><?php echo $row->cellphone; ?> </td>
					<td><?php echo $row->registeredby; ?> </td>
					<td><?php echo $row->skilllevel; ?> </td>
					<td><?php echo $row->playoffs; ?> </td>
				</tr>
<?php
}
?>
</tbody>
</table>
</div>

