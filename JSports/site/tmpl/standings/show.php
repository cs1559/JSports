<?php
/**
 * @package     Dashboard.Site
 * @subpackage  com_jsports
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use FP4P\Component\JSports\Administrator\Helpers\Html;
use Joomla\CMS\Component\ComponentHelper;

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.standings.style');

$params = ComponentHelper::getParams('com_jsports');
$showposition = $params->get('showposition');

?>

<div id="standings-container">

<?php if (!$this->program->registrationonly) { ?>

		<h1><span id="standings-league-description">League Standings - <?php echo $this->program->name; ?></span></h1>
		<br/>
		<span class="system_message"><?php // echo $season_note; ?></span>
		<div>
			<?php 
			foreach ($this->divisions as $div) {
			    ?>
			    <a href="#div<?php echo $div['id']?>"><?php echo $div['name'];?></a> |
			    
			<?php 
			}
			
			?>
		</div>
		<?php
			$x = 0;
			$prevdiv = 0;
			$first = true;
			foreach ($this->standings as $item) 
			{
				if ($x % 2) {
					$rowclass = "standings-row-even";
				} else {
					$rowclass = "standings-row-odd";
				}
				$x  += 1;
				if ($item['divisionid'] != $prevdiv) {
				    
					if ($first) {
						
					}  else { ?>
					    </table>
					    </div>
					    <a class="btn btn-primary btn-sm" href="#top">Back to Top</a>
					    <?php 
					}	 			
                    ?>
                    <div id="div<?php echo $item['divisionid']; ?>" class="jsports-table-wrapper">
					<h3 class="standings-division-title"><?php echo $item['divisionname'] ?></h3>
									
						<table class="table table-striped standings-table">
					
    					<tr class="standings-table-division-subheader-row">
    					<?php  if ($showposition) { ?>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_POSITION'); ?></td>
    						<?php  } ?>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_TEAMNAME'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_WINS'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_LOSSES'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_TIES'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_GP'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_POINTS'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_RUNSSCORED_ABBR'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_RUNSALLOWED_ABBR'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_DIFF'); ?></td>
    						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_WINPCT'); ?></td>
    					</tr>
    					
    					<?php 				
					
					//echo "start table - " . $item['divisionname'] . '<br/>';


					$prevdiv = $item['divisionid'];
				}
                ?>
				<tr class="<?php echo $rowclass; ?>">
				<?php
        				if ($showposition) {
				?>
				
					<td class="standings-table-stats-cell">
						<?php echo $item['position']; ?>
					</td>	
					<?php  } ?>									
					<td>
						<?php 
//							//$link		= JRoute::_( 'index.php?option=com_jleague&controller=teams&task=viewTeamProfile&teamid=' .$obj->getTeamId() );						
							//$link		= JRoute::_( 'index.php?option=com_jleague&controller=teams&task=viewTeamProfile&teamid=' .$obj->getSlug() . "&Itemid=9999999" );
							//echo JHTML::_('link', $link , $obj->getTeamName(), array('id' => "team".$obj->getTeamId() ));
							$link		= Route::_( 'index.php?option=com_jsports&view=team&id=' .$item['teamid'] );
							echo "<a href=\"" . $link . "\">" . $item['teamname'] . "</a>"; 
						
						?>
					</td>
			
					<td class="standings-table-stats-cell">
						<?php echo $item['wins'];?>
					</td>				
					<td class="standings-table-stats-cell">
						<?php echo $item['losses'];?>
					</td>
					<td class="standings-table-stats-cell">
						<?php echo $item['ties'];?>
					</td>			
					<td class="standings-table-stats-cell">
						<?php //echo $item['wins'] + $item['losses'] + $item['ties'];?>
						<?php echo $item['gamesplayed'];?>
					</td>			
					<td class="standings-table-stats-cell">
						<?php echo $item['points'];?>
					</td>
					<td class="standings-table-stats-cell">
						<?php echo $item['runsscored'];?>
					</td>
					<td class="standings-table-stats-cell">
						<?php echo $item['runsallowed'];?>
					</td>
					<td class="standings-table-stats-cell">
						<?php echo $item['runsscored'] - $item['runsallowed'];?>
						<?php // echo $obj->getRunsScored() - $obj->getRunsAllowed();?>
					</td>					
					<td class="standings-table-stats-cell">
						<?php
							//echo number_format($obj->getWinningPercentage(),4);
						   echo number_format($item['winpct'],4);
						   ?>
						
					</td>
				</tr>
				
				<?php 	

				$first = false;

 			} 
			echo "</table></div>";
			
	?>
			<a class="btn btn-primary btn-sm" href="#top">Back to Top</a>
			
	<?php } ?>
</div>

