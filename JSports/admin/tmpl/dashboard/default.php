<?php 

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useStyle('com_jsports.admin.style');

?>

<div id="dashboard-container">
  <div class="row" >
	<div class="brand mb-3 p-3">
			<h2><?php echo $this->title . " - " . $this->version; ?></h2>	
	</div>
  </DIV>
  <div class="row">
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
  				<a href="index.php?option=com_jsports&view=leagues">Organization</a>
  			</div>
  		</div>	
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
  				<a href="index.php?option=com_jsports&view=programs">Programs</a><br/>
  			</div>
  		</div>
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
				<a href="index.php?option=com_jsports&view=divisions">Divisions</a><br/>
			</div>
  		</div>
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
  				<a href="index.php?option=com_jsports&view=teams">Teams</a><br/>
  			</div>
  		</div>
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
  				<a href="index.php?option=com_jsports&view=venues">Venues</a><br/>
  			</div>
  		</div>
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
  				<a href="index.php?option=com_jsports&view=Registrations">Registrations</a><br/>
  			</div>
  		</div>
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
  				<a href="index.php?option=com_jsports&view=Programsetup">Program Setup</a><br/>
  			</div>
  		</div>
  		<div class="col">
			<div class="card border h-100 py-2 " style="min-height: 100px;">  		
  				<a href="index.php?option=com_jsports&view=Rosters">Rosters</a><br/>
  			</div>
  		</div>
  		<div class="col">
  			<div class="card border h-100 py-2 " style="min-height: 100px;">
  			<a href="index.php?option=com_jsports&view=Games">Games</a><br/>
  			</div>
  		</div>
  		
  </div>




</div>


<a href="index.php?option=com_jsports&view=leagues">Leagues/Organization</a><br/>
<a href="index.php?option=com_jsports&view=programs">Programs</a><br/>
<a href="index.php?option=com_jsports&view=divisions">Divisions</a><br/>
<a href="index.php?option=com_jsports&view=teams">Teams</a><br/>
<a href="index.php?option=com_jsports&view=venues">Venues</a><br/>
<a href="index.php?option=com_jsports&view=Registrations">Registrations</a><br/>
<a href="index.php?option=com_jsports&view=Programsetup">Program Setup</a><br/>
<a href="index.php?option=com_jsports&view=Rosters">Rosters</a><br/>
<a href="index.php?option=com_jsports&view=Games">Games</a><br/>


</div>
