<?php

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useStyle('com_jsports.admin.style');

?>

<div id="dashboard-container container">
	<div class="row">
		<div class="dashboard-heading brand mb-3 p-3">
			<h1 style="background: #32386c; color: #ffffff; padding: 5px; "><?php echo $this->title; ?></h1	>
		</div>
	</DIV>
	
	<div class="row g-3">
		<div class="col-12 col-lg-8">
			<div class="dashboard-cards">
    <a href="index.php?option=com_jsports&view=leagues" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-university"></i>
            <h2>Leagues</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=programs" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-trophy"></i>
            <h2>Programs</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=programsetup" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-puzzle-piece"></i>
            <h2>Program Setup</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=divisions" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-layer-group"></i>
            <h2>Divisions</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=teams" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-users"></i>
            <h2>Teams</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=venues" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-map-marker-alt"></i>
            <h2>Venues</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=registrations" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-baseball-ball"></i>
            <h2>Registrations</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=rosters" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-paste"></i>
            <h2>Rosters</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=games" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-calendar-days"></i>
            <h2>Games/Schedules</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=logs" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-newspaper"></i>
            <h2>View Logs</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=tools" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-screwdriver-wrench"></i>
            <h2>Tools</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=bulletins" class="card-link">
        <div class="dashboard-card">
            <i class="fas fa-bullhorn"></i>
            <h2>Bulletins</h2>
        </div>
    </a>

    <a href="index.php?option=com_jsports&view=dashboard" class="card-link">  <!-- adjust view if needed -->
        <div class="dashboard-card">
            <i class="fas fa-file-lines"></i>
            <h2>Reference Tables (Future)</h2>
        </div>
    </a>
    
    <a href="index.php?option=com_jsports&view=dashboard" class="card-link">  <!-- adjust view if needed -->
        <div class="dashboard-card">
            <i class="fas fa-file-lines"></i>
            <h2>Reports (Future)</h2>
        </div>
    </a>
</div>
		</div>
		<div class="col-12 col-lg-4" style="padding: 1rem;" >
			<h2>About JSports</h2>
			<p>
			Current Version: <?php echo $this->version; ?><br/>
            Release Date:  <?php echo $this->releasedate; ?>
			<hr/>
            <p>
            Copyright 2026 - Chris Strieter
            </p>
		</div>
	</div>
	
</div>	


</div>
