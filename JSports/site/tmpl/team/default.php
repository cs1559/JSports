<?php
/**
 * JSports Component for Joomla! 4.x
 *
 * @version     0.0.1
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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Set Page Title and load specific stylesheet
$document = Factory::getDocument();

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.teamprofile.style');

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
                    }
                    ?>
    			</div> <!--  END OF ACTIONS -->
    		</div> <!--  END OF ROW -->
		</div> <!--  end of section -->

		<hr class="hr-bar">

			<div class="teamprofile-section container">  <!--  LOGO/General Data -->
				<div class="row">
					<div class="teamprofile-logo col-lg-6 text-center">
						<img class="img-responsive" src="<?php echo $this->teamlogo;?>" />
					</div>
					<div class="teamprofile-info col-lg-6">
						<strong>Team ID:</strong><?php echo $this->data->id; ?><br /> <strong>Contact
							Name: </strong><?php echo $this->data->contactname; ?><br /> <strong>City/State:
						</strong><?php echo $this->data->city . ", " . $this->data->state; ?><br />
						<strong>Contact Phone:</strong><?php echo $this->data->contactphone; ?><br />
						<strong>Contact Email: </strong><?php echo $this->data->contactemail; ?><br />
						<strong>Website: </strong><?php echo $this->data->websiteurl; ?><br />
						<strong>Most Recent Program: </strong><?php echo $this->recentprogram->name; ?><br />
					</div>
				</div>
			</div>   <!--  end of section -->

			<div class="teamprofile-section container">  <!--  Team Staff Data -->
				<h3>Team Staff</h3>
				<hr class="hr-bar">
				
				<?php if (count($this->rosterstaff) > 0) { ?>
						<div class="teamprofile-table-wrapper">
            				<table class="table">
            					<thead>
            						<tr>
            							<th scope="col">Name</th>
            							<th scope="col">Role</th>
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


			<div class="teamprofile-section container">
					<h3>Team Statistics</h3>
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

			<div class="teamprofile-section container">
				<h3>Schedule - <?php echo $this->recentprogram->name; ?></h3>
					<hr class="hr-bar">
						<div class="teamprofile-table-wrapper">
								<table class="table">
									<thead>
										<tr>
											<th scope="col">Date</th>
											<th scope="col">Name</th>
											<th scope="col">W/L</th>
											<th scope="col">AS</th>
											<th scope="col">RS</th>
											<th scope="col">Status</th>
										</tr>
									</thead>
									<tbody>
	                            <?php
                                foreach ($this->games as $game) {
                                ?>
									<tr>
										<td><?php echo $game->gamedate; ?></td>
										<td><?php echo $game->name; ?></td>
										<td><?php echo GameService::getWinLoss($this->data->id,$game); ?></td>
										<td><?php echo $game->awayteamscore; ?></td>
										<td><?php echo $game->hometeamscore; ?></td>
										<td><?php echo $game->gamestatus; ?></td>
									</tr>
                            	<?php } ?>
									</tbody>
								</table>
						</div>
			</div>  <!--  end of section  -->

			<div class="teamprofile-section container">  <!--  Team Staff Data -->
				<h3>Team Roster</h3>
				<hr class="hr-bar">
				
				<?php 
				if ($this->canSeeRoster) {
				    if (count($this->rosterplayers) > 0) { ?>
						<div class="teamprofile-table-wrapper">
            				<table class="table">
            					<thead>
            						<tr>
            							<th scope="col">Name</th>
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
             				* No Players on Roster *
             	<?php 
                        }
				} else {
				    echo "No Players on File or not authorized to view roster";
				}
             	
             	
             	?>
			</div>   <!--  end of section -->




			<div class="teamprofile-section">						

						 <input type="hidden" name="task" value="">
						<?php echo HTMLHelper::_('form.token'); ?>

							<div class="control-group">
									<div class="controls">
										<button type="submit" class="btn btn-primary" name="view"
											value="Dashboard" formnovalidate>
											<span class="icon-times" aria-ahidden="true"></span>
            							<?php echo Text::_('COM_JSPORTS_DASHBOARD'); ?>
        </button>
										<input type="hidden" name="option" value="com_jsports"> 
									</div>  <!--  end of controls -->
								</div>  <!--  end of control group -->
			</div>	  <!--  end of section -->
	
	
	

		
	</form>

</div>  <!--  end of container -->