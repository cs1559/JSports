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
  
.jsports-img-link {
    display: flex;
    flex-direction: row;     /* <-- horizontal instead of vertical */
    align-items: center;     /* vertically center text with image */
    text-decoration: none;
}

.jsports-img-link img {
    max-width: 200px;        /* control image size */
    height: auto;
    margin-right: 12px;      /* space between image and text */
}

.jsports-img-link p {
    margin: 0;
    text-align: left;
}
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
            
        	<img src="<?php echo $campaign->getAssetUrl(); ?>?t=<?php echo time(); ?>" >
			<p>
				<?php echo $campaign->content; ?>
			</p>
        	<?php 
            if ($redirect) {
                echo "</a>";
            }
            ?>
    </div>
</div>
        