<?php
defined('_JEXEC') or die();
?>

<!-- Bootstrap 5 -->
<link
	href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
	rel="stylesheet">

<style>

.report-container {
	background: #ffffff;
	padding: 25px;
	border-radius: 8px;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
</style>

<div class="container report-container">

	<h3 class="mb-4">Teams with NO ROSTER</h3>

	<div class="table-responsive">
		<table class="table table-sm table-striped table-hover align-top">
			<thead class="table-light">
				<tr>
					<th style="width: 80px;">ID</th>
					<th>Team Name</th>
					<th>Contact Name</th>
					<th style="width: 110px;">Age Group</th>
					<th>Division</th>
				</tr>
			</thead>
			<tbody>

<?php
foreach ($displayData as $row) {
    ?>
    <tr>
					<td><?php echo $row->teamid; ?></td>
					<td><?php echo $row->teamname; ?> </td>
					<td><?php echo $row->contactname; ?> </td>
					<td><?php echo $row->agegroup; ?> </td>
					<td><?php echo $row->divisionname; ?> </td>
				</tr>
<?php
}
?>
</tbody>
		</table>
	</div>

</div>

