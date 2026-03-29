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

<div class="jsports-ad position-fixed top-0 start-0 w-100 d-flex justify-content-center"
     data-adid="5"
     data-type="campaign"
     id="jsports-sticky-5"
     style="z-index: 1055;">

    <div class="bg-dark text-white shadow d-flex align-items-center px-3 py-2 m-2 rounded w-100"
         style="max-width: 100%; position: relative;">

        <a class="d-flex align-items-center text-white text-decoration-none flex-grow-1"
           href="/baseball?task=campaign.click&id=5&ts=<?= time() ?>&sig=<?= $sig ?>"
           target="_blank">
j
            <img src="/images/sponsors/sample-ad.jpg"
                 alt="Sponsor Ad"
                 class="me-3 img-fluid"
                 style="max-height: 60px; width: auto;">

            <div>
                <div class="fw-bold">DICK'S Sporting Goods</div>
                <div class="small opacity-75">20% off this weekend</div>
            </div>
        </a>

        <button type="button"
                class="btn-close btn-close-white ms-3"
                aria-label="Close"
                onclick="document.getElementById('jsports-sticky-5').remove();">
        </button>
    </div>
</div>            

    </div>
</div>
        