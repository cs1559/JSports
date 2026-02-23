<?php
use FP4P\Component\JSports\Site\Helpers\JSHelper;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<div class="report-table-wrap mt-3">
<table class="table table-striped table-hover table-sm report-table">

<thead>
<tr>
  <th>Date</th>
  <th>Time</th>
  <th>Division</th>
  <th>Age Group</th>
  <th>Home Team</th>
  <th>Score</th>
  <th>Away Team</th>
  <th>Score</th>
  <th>Cross Division</th>
  <th>GameID</th>
</tr>
</thead>

<tbody>
<?php
foreach ($displayData as $row) {
    ?>
			<tr>
				<td><?php echo $row->gamedate; ?></td>
				<td><?php echo JSHelper::displayGameTime($row->gametime)?>
				<td><?php echo $row->divisionname; ?> </td>
				<td><?php echo $row->agegroup; ?> </td>
				<td><?php echo $row->hometeamname; ?> </td>
				<td><?php echo $row->hometeamscore; ?> </td>
				<td><?php echo $row->awayteamname; ?> </td>
				<td><?php echo $row->awayteamscore; ?> </td>
				<td><?php echo $row->crossdivisiongame; ?> </td>
				<td><?php echo $row->id; ?></td>
			</tr>
			<?php
}
?>
</tbody>
</table>
</div>

