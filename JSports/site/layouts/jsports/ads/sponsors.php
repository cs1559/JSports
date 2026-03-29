<?php
/**
 * Default.php - This is the default sponsorship template used to render
 * only the sponsor's logo with a click thru to their site.
 * 
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

$sponsor = $displayData['sponsor'];

?>

<div class="jsports-ad" data-adid="<?php echo $sponsor->id;?>" data-type="sponsor">

    <div class="jsports-sponsor jsports-sponsor-container">
    
         <div class="jsports-sponsor-logo">
            <a href="<?php echo $displayData['clickurl'];?>" target="_blank">
            	<img src="<?php echo $displayData['logo']; ?>?t=<?php echo time(); ?>" >
            </a>
            <?php if (!empty($sponsor->slogan)) { ?>
            	<p class="jsports-sponsor-slogan">
                <?php echo $sponsor->slogan; ?>
                </p>
            <?php }?>
        	
        </div>
    	
    </div>

</div>

