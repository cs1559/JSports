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
$wa->useStyle('com_jsports.teamprofile.style');

?>

    
<h1>Standings Body </h1>
<br/><br/>

<div id="standings-container">

<?php foreach ($this->standings as $item) { ?>

	<?php echo $item['divisionid'] . " -- " . $item['teamname'] . " -- " . $item["headcoach"]?><br/>

<?php } ?>

</div>

