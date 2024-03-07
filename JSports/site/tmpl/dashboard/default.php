<?php
/**
 * @package     Dashboard.Site
 * @subpackage  com_jsports
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/*
 * 
 * 	<ul>
		<li>Upcoming Games/Matches</li>
		<li>Recent Matches</li>
		<li>Average Run Differential by age group (Division?)</li>
		<li>Games Played vs. Games Scheduled</li>
	</ul>
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use FP4P\Component\JSports\Site\Objects\RouteHelper;

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useStyle('com_jsports.jsports.style');

?>
<div class="page-header">
</div>


<div class="container">
	<h2>Upcoming Games - <?php echo $this->program->name; ?></h2>
	
	<div class="jsports-table-wrapper">
	<table class="table">
  	<thead>
    	<tr>
      		<th class="w-10" scope="col">Date</th>
      		<th class="w-30" scope="col">Game</th>
      		<th class="w-30" scope="col">Location</th>
      		<th class="w-20" scope="col">Division</th>
    	</tr>
  	</thead>

	<?php foreach ($this->upcominggames as $game) {	?>
		<tr>
			<th scope="row"><?php echo $game->gamedate; ?></th>
			<td><?php echo $game->name; ?></td>
			<td><?php echo $game->location; ?></td>
			<td><?php echo $game->divisionname; ?></td>
		</tr>
	<?php } ?>

	</table>
	</div>
</div>


<!-- League Statistics -->
<div class="container">
	<h2>Statistics</h2>
	
	<?php echo "Total Teams= " . $this->stats->totalteams;?> </br>
	<?php echo "Total Games= " . $this->stats->totalgames;?> </br>
	<?php echo "Total Games Scheduled = " . $this->stats->gamesscheduled;?> </br>
	<?php echo "Total Games Completed= " . $this->stats->gamescompleted;?> </br>
	

</div>


