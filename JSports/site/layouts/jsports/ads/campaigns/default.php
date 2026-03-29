<?php
/**
 * Default.php - This is the default sponsorship template used to render
 * only the sponsor's logo with a click thru to their site.
 * 
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;


$campaign = $displayData['campaign'];
$redirect = (strlen($campaign->url) > 0) ? 1 : 0;

?>

<style>
<?php echo $campaign->customcss; ?>
</style>
<div class='jsports-campaign-container jsports-campaign-<?php echo htmlspecialchars($displayData['position'], ENT_QUOTES, 'UTF-8'); ?>-container'>

    <div id='jsports-campaign-<?php echo $displayData['position']; ?>' class='jsports-campaign-slot jsports-campaign-img-content {$this->classname}'>
            
            <?php 
            if ($redirect) {
                ?>
               	<a class='jsports-img-link' target='_blank' href='<?php echo $displayData['clickurl']; ?>' rel='noopener noreferrer'>
            <?php 
            }
            ?>
            
        	<img src="<?php echo $campaign->getSponsorLogoUrl(); ?>?t=<?php echo time(); ?>" >
        	
        	<?php 
            if ($redirect) {
                echo "</a>";
            }
            ?>
    </div>
</div>
        