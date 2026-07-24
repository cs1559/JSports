<?php
/**
 * Default.php - This is the default sponsorship template used to render
 * a two-column sponsor ad: the sponsor's logo (linked to their site) in
 * one column, and text content in the other.
 *
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;


$campaign = $displayData['campaign'];
$redirect = (strlen($campaign->url) > 0) ? 1 : 0;

?>

<style>
<?php echo $campaign->customcss; ?>

.jsports-ad,
.jsports-campaign-container,
.jsports-campaign-slot {
    /* the ad only ever gets as WIDE as the slot it's dropped into on the
       page, so cap width only. Height must stay auto so the box grows
       to fit whatever content the sponsor supplies instead of clipping
       it — some page/theme CSS outside this template sets a fixed
       height on these ad wrappers, so we override that explicitly. */
    max-width: 100%;
    box-sizing: border-box;
    height: auto !important;
    min-height: 0;
    overflow: visible !important;
}

.jsports-two-col {
    display: flex;
    flex-direction: row;     /* two columns, side by side */
    align-items: center;     /* vertically center text with image */
    flex-wrap: nowrap;
    gap: 12px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    height: auto;
    overflow: visible;        /* let the row grow to fit wrapped text instead of cropping it */
}

.jsports-img-col {
    /* Sized as a share of THIS container's own width (not the browser
       viewport) so it scales correctly even when the ad sits in a narrow
       slot on a wide desktop screen. flex-basis % resolves against the
       flex row's actual rendered width, which is the ad's own width. */
    flex: 0 1 35%;
    max-width: 200px;         /* absolute cap for large ad slots */
    min-width: 48px;          /* stay visible in very narrow slots */
    box-sizing: border-box;
    display: block;
    text-decoration: none;
}

.jsports-img-col img {
    width: 100%;              /* fill the column, which is already sized relative to the ad */
    max-width: 100%;
    height: auto;
    display: block;
}

.jsports-text-col {
    flex: 1 1 0;               /* text column fills remaining space */
    min-width: 0;              /* allow text to wrap instead of overflowing */
    max-width: 100%;
    overflow-wrap: break-word;
    word-break: break-word;
}

.jsports-text-col p {
    margin: 0;
    text-align: left;
    font-size: 1rem;
    line-height: 1.4;
    max-width: 100%;
    overflow-wrap: break-word;
    word-break: break-word;
}

/* On narrow devices, stack the image above the text instead of squeezing
   both into a cramped row. */
@media (max-width: 480px) {
    .jsports-two-col {
        flex-direction: column;
        align-items: flex-start;
    }

    .jsports-img-col {
        flex-basis: auto;
        max-width: 60%;
    }

    .jsports-text-col p {
        font-size: 0.9rem;
    }
}
</style>
<div class='jsports-campaign-container jsports-campaign-<?php echo htmlspecialchars($displayData['position'], ENT_QUOTES, 'UTF-8'); ?>-container'>

    <div id='jsports-campaign-<?php echo $displayData['position']; ?>' class='jsports-campaign-slot jsports-campaign-img-content jsports-two-col <?php echo $campaign->classname; ?>'>

        <?php if ($redirect) { ?>
            <a class='jsports-img-col' target='_blank' href='<?php echo $displayData['clickurl']; ?>' rel='noopener noreferrer'>
                <img src="<?php echo $campaign->getAssetUrl(); ?>?t=<?php echo time(); ?>" >
            </a>
        <?php } else { ?>
            <div class='jsports-img-col'>
                <img src="<?php echo $campaign->getAssetUrl(); ?>?t=<?php echo time(); ?>" >
            </div>
        <?php } ?>

        <div class='jsports-text-col'>
            <?php echo $campaign->content; ?>
        </div>

    </div>
</div>
