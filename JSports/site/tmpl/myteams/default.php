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
        <?php foreach ($this->teams as $item) { ?>
            
            <div class="flex-child card ">
  				<div class="card-body">
				    <h4 class="card-title"><?php echo $item->name; ?></h4>
				    <p class="card-text"><?php echo $item->lastprogramname; ?></p>
    				<a href="<?php echo Route::_('index.php?option=com_jsports&view=team&id=' . $item->id);?>" class="btn btn-primary">See Profile</a>
  				</div>
			</div>
            
        <?php } ?>
        
</div>
        <?php endif; ?>