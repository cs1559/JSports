<?php

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useStyle('com_jsports.admin.style');

?>

<div id="dashboard-container container">
	<div class="row">
		<div class="dashboard-heading brand mb-3 p-3">
			<h2><?php echo $this->title; ?></h2>
		</div>
	</DIV>
	
	<div class="row g-3">
		<div class="col-12 col-lg-8">
			<div class="dashboard-cards row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xxl-4 g-3 mb-3 text-center">
				<div class="col">
					<a href="index.php?option=com_jsports&view=leagues">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Leagues</h2>
    							<i class="fas fa-university fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
    			</div>
			
				<div class="col">
					<a href="index.php?option=com_jsports&view=programs">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Programs</h2>
    							<i class="fas fa-trophy fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
    			</div>
    			
				<div class="col">
					<a href="index.php?option=com_jsports&view=programsetup">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Program Setup</h2>
    							<i class="fas fa-puzzle-piece fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
    			</div>
    			
    			<div class="col">
    				<a href="index.php?option=com_jsports&view=divisions">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Divisions</h2>
    							<i class="fas fa-layer-group fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
				</div>
    			<div class="col">
    				<a href="index.php?option=com_jsports&view=teams">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Teams</h2>
    							<i class="fas fa-users fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
				</div>				
    			<div class="col">
    				<a href="index.php?option=com_jsports&view=venues">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Venues</h2>
    							<i class="fas fa-map-marker-alt fa-3x"></i>
    						</p>
    					</div>
    				</div>
    				</a>
				</div>				

    			<div class="col">
    				<a href="index.php?option=com_jsports&view=registrations">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Registrations</h2>
    							<i class="fas fa-baseball-ball fa-3x"></i>
    						</p>
    					</div>
    				</div>
    				</a>
				</div>
				
    			<div class="col">
    				<a href="index.php?option=com_jsports&view=rosters">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Rosters</h2>
    							<i class="fas fa-paste fa-3x"></i>
    						</p>
    					</div>
    				</div>
    				</a>
				</div>
				
    			<div class="col">
    				<a href="index.php?option=com_jsports&view=games">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Games/Schedules</h2>
    							<i class="fas fa-calendar-days fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
				</div>

    			<div class="col">
    				<a href="index.php?option=com_jsports&view=logs">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>View Logs</h2>
    							<i class="fas fa-newspaper fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
				</div>
				
    			<div class="col">
    				<a href="index.php?option=com_jsports&view=tools">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Tools</h2>
    							<i class="fas fa-screwdriver-wrench fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
				</div>	
				
    			<div class="col">
    				<a href="index.php?option=com_jsports&view=dashboard">
    				<div class="card h-100 p-2 m-1">
    					<div style="text-align: center;">
    						<p class="card-text" ><h2>Reports (Future)</h2>
    							<i class="fas fa-file-lines fa-3x"></i>
    						</p>
    						
    					</div>
    				</div>
    				</a>
				</div>
				
			</div>
		</div>
		<div class="col-12 col-lg-4">
			<h2>About JSports</h2>
			<p>
			Current Version: <?php echo $this->version; ?><br/>
            Release Date:  <?php echo $this->releasedate; ?>

            </p>
		</div>
	</div>
	
</div>	


</div>
