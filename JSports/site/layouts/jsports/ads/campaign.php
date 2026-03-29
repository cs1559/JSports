<?php
/**
 * Default.php - This is the default sponsorship template used to render
 * only the sponsor's logo with a click thru to their site.
 * 
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

?>

<style>
<?php echo $customcss; ?>
</style>
<div id="mod-sponsor-campaign-<?php echo $moduleid;?>" class="<?php echo $styling; ?> mod_jsports_module_container">

    <div  class="sponsor-campaign-wrapper sponsor-campaign-module-<?php echo $moduleid;?>">
    	<h3>Campaign Layout - <?php echo $moduleid; ?></h3>
    	<?php echo $campaign->title;?>
    </div>

</div>
