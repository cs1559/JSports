<?php
/**
 * JSports - Joomla Sports Management Component
 *
 * @version     1.0.0
 * @package     JSports
 * @subpackage  Site.Templates
 * @category    Teams
 * @copyright   Copyright (C) 2023-2024 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use FP4P\Component\JSports\Site\Services\GameService;
use FP4P\Component\JSports\Administrator\Helpers\JSHelper;
use Joomla\CMS\Component\ComponentHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Set Page Title and load specific stylesheet
$document = Factory::getDocument();

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.teamprofile.style');

$params = ComponentHelper::getParams('com_jsports');
$showlinks = $params->get('showpagelinks');
$showstandings = $params->get('showstandings');
$showlocation = $params->get('showlocation');
$showhits = $params->get('showhits');
$downloadschedule = $params->get('downloadcvsschedule');

$document->setTitle(Text::_('COM_JSPORTS_TEAMPROFILE_PAGE_TITLE'));
?>

<div class="container">
	<form
		action="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $this->data->id); ?>"
		method="post" name="profileform" id="profile-form"
		class="form-validate">


		<div class="teamprofile-section container">  <!--  Header/Menu -->
			<div class="row">
				<div class="teamprofile-header-name">
					<h1><?php echo $this->data->name; ?> Team Profile</h1>
				</div>
			</div>  <!--  END OF ROW -->
			<div class="row">
				<div class="teamprofile-header-actions">
    		<?php
                    if ($this->canEdit) {
                        echo $this->profilemenu;
                        
                        if (!$this->data->tournament) {
                            ?>
                            <br/>
                            <div class="alert alert-warning" role="alert">
	                            <strong>NOTE:  Team is NOT currently registered to play in the year end tournament</strong>
                            </div>
                            <?php 
                        }
                    }
                    ?>
    			</div> <!--  END OF ACTIONS -->
    		</div> <!--  END OF ROW -->
    		

		</div> <!--  end of section -->

		<hr class="hr-bar">

			<div class="teamprofile-section container">  <!--  LOGO/General Data -->
				<div class="row">
					<div class="teamprofile-logo col-lg-3 text-center">
						<img class="img-responsive" src="<?php echo $this->teamlogo;?>" />
					</div>
					<div class="teamprofile-info col-lg-5">
						<strong>Team ID:</strong>&nbsp;<?php echo $this->data->id; ?><br /> 
						<strong>Contact Name: </strong>&nbsp;<?php echo $this->data->contactname; ?><br /> 
						<?php if ($showlocation) {?>
							<strong>City/State:</strong>&nbsp;<?php echo $this->data->city . ", " . $this->data->state; ?><br />
						<?php } ?>
						<strong>Contact Phone:</strong>&nbsp;<?php echo $this->data->contactphone; ?><br />
						<strong>Contact Email: </strong>&nbsp;<?php echo $this->data->contactemail; ?><br />
						<strong>Website: </strong>&nbsp;
						<?php
						if (!is_null($this->data->websiteurl)) {
    						if (strlen($this->data->websiteurl) > 5) {
    						      echo $this->data->websiteurl; 
    						}
						} else {
						    echo 'N/A';
						}
					   ?><br />
						      					
					</div>
					
					<div class="teamprofile-info col-lg-4">
						<strong>Most Recent Program: </strong>&nbsp;<?php echo $this->recentprogram->name; ?><br />
						<strong>Status:</strong>&nbsp;<?php 
						if ($this->active) {
						    echo Text::_('COM_JSPORTS_ACTIVE');
						} else {
						    echo Text::_('COM_JSPORTS_NONACTIVE');  
						}?> <br/>
						<?php if ($showhits) { ?>
							<strong>Hits:</strong>&nbsp;<?php echo $this->data->hits; ?><br />
						<?php } ?>
					</div>
				</div>
			</div>   <!--  end of section -->

<?php if ($showlinks) { ?>
    		<div id="bookmarks">Page Links:
    			<a href="#teamstaff">Team Staff</a> |
    			<a href="#divisionstandings">Division Standings</a> |
    			<a href="#teamstats">Team Stats</a> |
    			<a href="#schedule">Schedule</a> |
    			<a href="#roster">Roster</a>
    		</div>
<?php } ?>
    		
			<div class="teamprofile-section container">  <!--  Team Staff Data -->
				<h3 id="teamstaff"><?php echo Text::_('COM_JSPORTS_PROFILE_STAFF_HEADING'); ?></h3>
				<hr class="hr-bar">
				
				<?php if (count($this->rosterstaff) > 0) { ?>
						<div class="teamprofile-table-wrapper">
            				<table class="table">
            					<thead>
            						<tr>
            							<th scope="col"><?php Text::_('COM_JSPORTS_NAME'); ?></th>
            							<th scope="col"><?php Text::_('COM_JSPORTS_ROLE'); ?></th>
            						</tr>
            					</thead>
            					<tbody>
            	
            				<?php 
                        	foreach ($this->rosterstaff as $staffitem) {
                            ?>
                            	<tr>
                            		<td><?php echo $staffitem->firstname . " " . $staffitem->lastname; ?> </td>
                            		<td><?php echo $staffitem->role; ?>
                            	</tr>
                            <?php } ?>
                            
                            </tbody>
                            </table>
                        </div>
             	<?php } else {    ?>
             				* See Contact Information above *
             	<?php }?>
			</div>   <!--  end of section -->

<?php if ($showstandings) { ?>

			<div class="teamprofile-section container">  <!--  Divisional Standings -->
				<h3 id="divisionstandings"><?php echo Text::_('COM_JSPORTS_PROFILE_STANDINGS_HEADING'); ?>&nbsp; (<?php echo $this->divisionname; ?>)</h3>
				<hr class="hr-bar">
				
				<?php if (count($this->standings) > 0) { ?>
						<div class="teamprofile-table-wrapper">
            				<table class="table">
            					<thead>
            						<tr>
            							<th scope="col"><?php echo Text::_('COM_JSPORTS_TEAMNAME'); ?></th>
										<th scope="col">W</th>
										<th scope="col">L</th>
										<th scope="col">T</th>
										<th scope="col">RA</th>
										<th scope="col">RS</th>
										<th scope="col">Pts</th>
            						</tr>
            					</thead>
            					<tbody>
            	
            				<?php 
                        	foreach ($this->standings as $standingsitem) {
                            ?>
                            	<tr>
                            		<td>
                            			<a href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $standingsitem['teamid']); ?>">
                            			<?php echo $standingsitem['teamname']; ?>
                            			</a> 
                        			</td>
                            		<td><?php echo $standingsitem['wins']; ?>
                            		<td><?php echo $standingsitem['losses']; ?>
                            		<td><?php echo $standingsitem['ties']; ?>
                            		<td><?php echo $standingsitem['runsallowed']; ?>
                            		<td><?php echo $standingsitem['runsscored']; ?>
                        			<td><?php echo $standingsitem['points']; ?>
                            	</tr>
                            <?php } ?>
                            
                            </tbody>
                            </table>
                        </div>
             	<?php } else {    ?>
             				* See Contact Information above *
             	<?php }?>
			</div>   <!--  end of section -->

<?php } ?>
			<div class="teamprofile-section container">
					<h3 id="teamstats"><?php echo Text::_('COM_JSPORTS_PROFILE_STATISTICS_HEADING'); ?></h3>
					<hr class="hr-bar">
						<div class="teamprofile-table-wrapper">
								<table class="table">
									<thead>
										<tr>
											<th scope="col">Program</th>
											<th scope="col">Division</th>
											<th scope="col">W</th>
											<th scope="col">L</th>
											<th scope="col">T</th>
											<th scope="col">RA</th>
											<th scope="col">RS</th>
											<th scope="col">Pts</th>
										</tr>
									</thead>
									<tbody>
    
                            <?php
                            foreach ($this->programstats as $item) {
                                ?>
                            <tr>
											<td><a
												href="<?php

                                echo Route::_('index.php?option=com_jsports&view=schedules&teamid=' . $item->teamid . '&programid=' . $item->programid);
                                ?>"><?php echo $item->programname; ?></a></td>
											<td><?php echo $item->divisionname; ?> </td>
											<td><?php echo $item->wins; ?> </td>
											<td><?php echo $item->losses; ?> </td>
											<td><?php echo $item->ties; ?> </td>
											<td><?php echo $item->runsallowed; ?> </td>
											<td><?php echo $item->runsscored; ?> </td>
											<td><?php echo $item->points; ?> </td>
										</tr>
    
                            <?php } ?>
                            
                            </tbody>
							</table>
						</div>
			</div>  <!--  end of section  -->

			<!--  SCHEDULE SECTION -->
			<div class="teamprofile-section container">   
				<h3 id="schedule">Schedule - <?php echo $this->recentprogram->name; ?>
				<?php if ($downloadschedule) { ?>
						<a href="index.php?option=com_jsports&task=team.downloadSchedule&tmpl=component&teamid=<?php echo $this->teamid; ?>&programid=<?php echo $this->programid;?>" target="new" title="Download as CSV" download>
							<span class="fa fa-solid fa-download fa-sm mt-4 me-2" aria-hidden="true" style="float: right;"></span>
						</a>
				<?php } ?>
				</h3>
					<hr class="hr-bar">
						<div class="teamprofile-table-wrapper">
								<table class="table">
									<thead>
										<tr>
											<th scope="col">Date</th>
											<th scope="col">Time</th>
											<th scope="col">Name</th>
											<th scope="col">W/L</th>
											<th scope="col">Away</th>
											<th scope="col">Home</th>
											<th scope="col">Status*</th>
										</tr>
									</thead>
									<tbody>
	                            <?php
                                foreach ($this->games as $game) {
                                ?>
									<tr>
										<td><?php echo $game->gamedate; ?></td>
										<td><?php echo JSHelper::displayGameTime($game->gametime);?> </td>
										<td><?php echo $game->name; 
    										// Display indicator for NON-LEAGUE GAME
    										if (!$game->leaguegame) {
    										    echo '**';
    										}
    										?>
    										<br/>
    										<span class="game-location"><?php echo $game->location; ?></span>
										</td>
										<td><?php echo GameService::getWinLoss($this->data->id,$game); ?></td>
										<td><?php echo $game->awayteamscore; ?></td>
										<td><?php echo $game->hometeamscore; ?></td>
										<td><?php echo $game->gamestatus; ?></td>
									</tr>
                            	<?php } ?>
									</tbody>
								</table>
								<span class="game-legend"><?php echo Text::_('COM_JSPORTS_GAME_LEGEND'); ?></span>
								<br/>
						</div>
			</div>  <!--  end of section  -->

			<!-- ROSTER SECTION -->
			<div class="teamprofile-section container">  <!--  Team Staff Data -->
				<h3 id="roster"><?php echo Text::_('COM_JSPORTS_PROFILE_ROSTER_HEADING'); ?></h3>
				<hr class="hr-bar">
				
				<?php 
				if ($this->canSeeRoster) {
				    if (count($this->rosterplayers) > 0) { ?>
						<div class="teamprofile-table-wrapper">
            				<table class="table">
            					<thead>
            						<tr>
            							<th scope="col">Name</th>
        								<th scope="col">No.</th>
            						</tr>
            					</thead>
            					<tbody>
            	
            				<?php 
                        	foreach ($this->rosterplayers as $rosteritem) {
                            ?>
                            	<tr>
                            		<td><?php echo $rosteritem->firstname . " " . $rosteritem->lastname; ?> </td>
                            		<td><?php echo $rosteritem->playernumber; ?>
                            	</tr>
                            <?php } ?>
                            
                            </tbody>
                            </table>
                        </div>
             	<?php } else {    ?>
             				* No Players on Roster *
             	<?php 
                        }
				} else {
				    echo "No Players on File or not authorized to view roster";
				}
             	
             	
             	?>
			</div>   <!--  end of section -->


			<div class="teamprofile-section">									
			</div>	  <!--  end of section -->
	
	
	
		 <input type="hidden" name="task" value="">
			<?php echo HTMLHelper::_('form.token'); ?>

		
	</form>

</div>  <!--  end of container -->