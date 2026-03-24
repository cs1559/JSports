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
use FP4P\Component\JSports\Site\Campaigns\CampaignManager;
use FP4P\Component\JSports\Site\Ads\AdsManager;


// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useScript('com_jsports.jsports.script');
$wa->useScript('com_jsports.standings.script');
$wa->useStyle('com_jsports.jsports.style');
$wa->useStyle('com_jsports.campaigns.style');

?>

<?php
    // Display any content for standings-top position
//     echo CampaignManager::renderCampaigns('standings-top');
    echo AdsManager::renderCampaign('standings-top');
?>
    
<div id="top" class="page-header">
	<h1><?php echo "Standings"; ?></h1>
</div>
<div hidden id="spinner"></div>
<form action="<?php echo Route::_('index.php?option=com_jsports&view=Dashboard'); ?>"
	method="post" name="standingsform" id="standings-form" class="form-validate">
	
<?php echo Html::getProgramsList(); ?>

</form>

<br/><br/>

<!--  <div class="jsports-table-wrapper">   -->
<div id="standings-container">

</div>
<!--  </div> -->

<?php
    // Display any content for standings-top position
    echo AdsManager::renderCampaign('standings-bottom');
?>