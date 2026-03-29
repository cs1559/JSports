<?php
/**
 * Default.php - This is the default sponsorship template used to render
 * only the sponsor's logo with a click thru to their site.
 * 
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

extract($displayData);

?>

<style>
<?php //echo $customcss; ?>
</style>
<div class='jsports-campaign-container jsports-campaign-<?php echo htmlspecialchars($displayData['position'], ENT_QUOTES, 'UTF-8'); ?>-container'>
<div class='jsports-text-campaign-container' style='width:100%; '>

    <div id='jsports-campaign-<?php echo $campaign->id; ?>-<?php echo $position; ?>' class='jsports-text-campaign-content <?php echo $data->classname; ?>'>
    	<?php echo $campaign->content;?>
    </div>
        
</div>
</div>