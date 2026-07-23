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
    width: 100%;
    height: 100px;
    box-sizing: border-box;
}

.jsports-two-col {
    display: flex;
    flex-direction: row;     /* two columns, side by side */
    align-items: center;     /* vertically center text with image */
    flex-wrap: nowrap;
    gap: 12px;
    width: 100%;
    height: 100%;
    box-sizing: border-box;
    /* lets the text below scale itself off the row's own rendered width
       via cqw units, rather than the browser viewport */
    container-type: inline-size;
    container-name: jsports-ad-row;
}

.jsports-img-col {
    /* Sized as a share of the row's own width (not the browser viewport),
       so it scales correctly no matter how big or small the ad slot is. */
    flex: 0 1 35%;
    min-width: 40px;          /* stay visible in very narrow slots */
    height: 100%;
    box-sizing: border-box;
    display: block;
    text-decoration: none;
}

.jsports-img-col img {
    /* Fills the column in both directions and scales with it. If
       .jsports-ad (or whatever wraps it) has an actual defined height
       set by the page/theme, this height:100% chain carries that size
       all the way down and object-fit:contain scales the logo to match
       it (no cropping, aspect ratio preserved). If nothing up the chain
       sets an explicit height, percentage heights fall back to the
       image's natural width-based scaling automatically, so this is
       safe either way. */
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}

.jsports-text-col {
    flex: 1 1 0;               /* text column fills remaining space */
    min-width: 0;              /* allow text to wrap instead of overflowing */
    box-sizing: border-box;
    overflow-wrap: break-word;
    word-break: break-word;
}

.jsports-text-col p {
    margin: 0;
    text-align: left;
    /* shrinks a bit as the row narrows, within a readable range */
    font-size: clamp(0.8rem, 1.5cqw + 0.6rem, 1rem);
    line-height: 1.4;
    overflow-wrap: break-word;
    word-break: break-word;
}

/* Fallback for browsers that don't understand container query units —
   cqw is simply invalid there, so give them a fixed, safe font-size. */
@supports not (width: 1cqw) {
    .jsports-text-col p {
        font-size: 0.9rem;
    }
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
        width: 60%;
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
