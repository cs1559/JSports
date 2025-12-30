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

// Load Web Asset Manager
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jsports');
$wa->useStyle('com_jsports.myteams.style');
?>

<div class="page-header">
	<h1><?php echo "My Teams"; ?></h1>
</div>

<br/><br/>
<?php if (empty($this->teams)) : 
	       ?>
	      
		
		<div class="alert alert-info">
			<span class="fa fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::_('COM_JSPORTS_NO_TEAMS_FOUND') . " or " . Text::_('COM_JSPORTS_NOT_LOGGEDIN'); ?>
		</div>
		
	<?php else : ?>
<div class="flex-container">


<div class="d-flex flex-wrap gap-3 align-items-stretch"> <!--   THIS IS THE TOP d-flex  -->
        <?php foreach ($this->teams as $item) { ?>
            
            <?php 
            if (empty($item->logo)) {
                $logo = $this->defaultlogo;
            } else {
                if (strlen($item->logo) > 0) {
                    $teamlogo = $this->logodir . "/Teamid-" . $item->id . "/" . $item->logo;
                    if (!file_exists(JPATH_SITE . $teamlogo)) {
                        $logo = $this->defaultlogo;
                    } else {
                        $logo = $teamlogo;
                    }
                } 
            }
            ?>
     <div class="card team-card-wrapper">       
   

            <!-- Logo -->
            <div class="team-logo">
                <img src="<?php echo $logo;?>"
                     alt="<?php echo $item->name; ?>logo"
                     class="img-fluid">
            </div>

            <!-- Title -->
            <h4 class="card-title mb-1"><?php echo $item->name; ?></h4>

            <!-- Season -->
            <p class="card-text text-muted"><?php echo $item->lastprogramname; ?></p>

            <!-- Button -->
            <a href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $item->id); ?>"
               class="btn btn-primary mt-auto">
                See Profile
            </a>

   
    </div>    
                
        <?php } ?>
        
</div>   <!-- THIS IS THE BOTTOM -->

</div>
        <?php endif; ?>