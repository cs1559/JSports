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

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useStyle('com_jsports.standings.style');

?>

<div id="standings-container">

		<h1><span id="standings-league-description">League Standings - <?php echo $this->program->name; ?></span></h1>
		<br/>
		<span class="system_message"><?php // echo $season_note; ?></span>

		<?php
			$x		= 0;
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
					//$divobj = $divdao->findById($obj->getDivisionId());
					if ($first) {
						?>
					<table class="standings-table">
						<?php
					}  				
					?>
					<tr class="standings-table-division-header-row"><td class="standings-table-division-header" colspan="99"><a name="divid-<?php echo $item['divisionid'];?>"><?php echo $item['divisionname']; ?></a></td></tr>
					
					<tr class="standings-table-division-subheader-row">
						<?php // if ($_config->getProperty('show_position_in_standings')) { ?>
						<td class="standings-table-stats-cell-header"><?php echo Text::_('COM_JSPORTS_POSITION'); ?></td>
						<?php // } ?>
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
					$prevdiv = $item['divisionid'];
				}
	?>
				<tr class="<?php echo $rowclass; ?>">
					<?php 
					//if ($_config->getProperty('show_position_in_standings')) {
					?>
				
					<td class="standings-table-stats-cell">
						<?php echo $item['position']; ?>
					</td>	
					<?php // } ?>									
					<td>
						<?php 
//							//$link		= JRoute::_( 'index.php?option=com_jleague&controller=teams&task=viewTeamProfile&teamid=' .$obj->getTeamId() );						
							//$link		= JRoute::_( 'index.php?option=com_jleague&controller=teams&task=viewTeamProfile&teamid=' .$obj->getSlug() . "&Itemid=9999999" );
							//echo JHTML::_('link', $link , $obj->getTeamName(), array('id' => "team".$obj->getTeamId() ));
							echo $item['teamname']; 
						
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
						<?php echo $item['wins'] + $item['losses'] + $item['ties'];?>
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
						   echo $item['winpct'];
						   ?>
						
					</td>
				</tr>
	<?php
			}
	?>
			</table>
	


</div>

