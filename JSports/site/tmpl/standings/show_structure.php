<?php
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
						echo "*** FIRST DIVISION *** <br/>";
					}  else {
						echo "end table <br/>";
					}	 			

					echo '<h3>' . $item['divisionname'] . '</h3>';
					echo "start table - " . $item['divisionname'] . '<br/>';


					$prevdiv = $item['divisionid'];
				}

				echo $item['teamname'] . "<br/>";
					

				$first = false;

 			} 
			echo "end table <br/>";
	?>

</div>

