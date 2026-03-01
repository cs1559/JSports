<?php
/**
 * JSports - Joomla Sports Management Component 
 *
 * @version     1.4.0
 * @package     JSports.Administrator
 * @subpackage  com_jsports
 * @copyright   Copyright (C) 2023-2026 Chris Strieter
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use FP4P\Component\JSports\Site\Helpers\SponsorHelper;
use FP4P\Component\JSports\Site\Helpers\JSHelper;
use FP4P\Component\JSports\Site\Helpers\FileHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$sponsorships = $this->sponsorships ?? [];
$assets = $this->assets ?? [];

$return = base64_encode('index.php?option=com_jsports&view=sponsor&layout=edit&id=' . (int) $this->item->id);

?>

<form enctype="multipart/form-data" action="<?php echo Route::_('index.php?option=com_jsports&view=sponsor&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="sponsor-form" class="form-validate">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div>
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_JSPORTS_DETAILS')); ?>
		<div class="row">   <!-- GOOD -->
			<div class="col-md-6"> 
				<div class="row">  <!-- GOOD -->
					<div class="col-md-10">
						<?php echo $this->form->renderField('id'); ?>
						<?php echo $this->form->renderField('contactname'); ?>
						<?php echo $this->form->renderField('contactphone'); ?>
						<?php echo $this->form->renderField('contactemail'); ?>
						<?php echo $this->form->renderField('website'); ?>
						<?php echo $this->form->renderField('logo'); ?>
						<?php echo $this->form->renderField('afile'); ?>
					</div>
				</div>
			</div>  <!-- GOOD -->
			<div class="col-md-6">
			<?php if (strlen($this->item->logo) > 0) {?>
				<div class="sponsor-logo-wrapper">  <!-- GOOD -->
					<img class="sponsor-logo img-responsive"
						src="<?php echo SponsorHelper::getLogoURL($this->item->id, $this->item->logo);?>?t=<?php echo time(); ?>" />
					<a class="btn btn-danger btn-sm"
						onClick="return confirm('Are you sure?');"
						href="<?php echo Route::_('index.php?option=com_jsports&task=sponsor.deletelogo&id=' . $this->item->id
                        	      . '&return=' . $return); ?>"> Delete Logo
					</a>
				</div>  <!-- GOOD -->
			<?php } ?>
			
			
			<?php 
// ===============================================================================
//                          SPONSORSHIP TAB
// ===============================================================================
?>
				<div class="card card-light">  <!-- GOOD -->
					<div class="card-body">  <!-- GOOD -->
						<?php //echo LayoutHelper::render('joomla.edit.global', $this); ?>
						<h3>Sponsorship(s)</h3>
<?php if (empty($sponsorships)) : ?>
                    	<div class="alert alert-info">   <!-- GOOD -->
                    		<?php echo Text::_('COM_JSPORTS_ERR_NO_SPONSORSHIPS_FOUND'); ?>
                    	</div>
<?php else : ?>
						<div class="table-responsive">   <!-- GOOD -->
							<table class="table table-striped align-middle">
								<thead>
									<tr>
										<th><?php echo Text::_('COM_JSPORTS_PROGRAM'); ?></th>
										<th><?php echo Text::_('COM_JSPORTS_SPONSORSHIP_LEVEL'); ?></th>
										<th><?php echo Text::_('COM_JSPORTS_SPONSORSHIP_TYPE'); ?></th>
										<th><?php echo Text::_('COM_JSPORTS_STARTDATE'); ?></th>
										<th><?php echo Text::_('COM_JSPORTS_ENDDATE'); ?></th>
										<th><?php echo Text::_('COM_JSPORTS_ACTIONS'); ?></th>
									</tr>
								</thead>
								<tbody>
				<?php foreach ($sponsorships as $i => $row) : ?>
					<tr>
										<td><?php echo htmlspecialchars($row->programname ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars(SponsorHelper::translatePlanLevel($row->planlevel) ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars(SponsorHelper::translatePlanType($row->plantype) ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row->startdate ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($row->enddate ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php //echo htmlspecialchars(JSHelper::translateYesNo($row->published) ?? '', ENT_QUOTES, 'UTF-8'); ?>

	                      	<a class="btn btn-primary btn-sm"
											href="<?php echo Route::_('index.php?option=com_jsports&view=sponsorship&layout=edit&id=' . $row->id
	                        	      . '&return=' . $return); ?>"> 
	                        	Edit 
                	      	</a>
							<a class="btn btn-danger btn-sm"
											onClick="return confirm('Are you sure?');"
											href="<?php echo Route::_('index.php?option=com_jsports&task=sponsorship.delete&id=' . $row->id 
	                        	    . '&return=' . $return); ?>"> 
	                       		Delete 
	                       	</a>

						</td>
									</tr>
				<?php endforeach; ?>
			</tbody>
							</table>
						</div>
<?php endif; ?>
					</div>   <!-- GOOD -->
				</div> <!--  end of card-light  -->
			</div>  <!--  end of right side  -- GOOD -->
		</div>  <!--  end of row -->
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

<?php 
// ===============================================================================
//                          ASSET TAB
// ===============================================================================
?>
<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'assets', Text::_('COM_JSPORTS_SPONSOR_ASSETS')); ?>
<div class="row">
  <div class="col-md-12">

    <?php if (empty($assets)) : ?>
      <div class="alert alert-info">
        <?php echo Text::_('COM_JSPORTS_ERR_NO_SPONONSOR_ASSETS_FOUND'); ?>
      </div>
    <?php else : ?>

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        <?php foreach ($assets as $i => $asset) : ?>
          <div class="col">
            <div class="card h-100">
              <div class="card-header">
                <?php echo htmlspecialchars($asset->title ?? '', ENT_QUOTES, 'UTF-8'); ?>
              </div>

              <img class="card-img-top img-fluid w-100"
                   src="<?php echo SponsorHelper::getAssetURL($this->item->id, $asset->filename); ?>?t=<?php echo time(); ?>"
                   style="height:180px; object-fit:cover;" />

              <div class="card-body">
                <p class="card-text">
                  <strong>Description: </strong><?php echo $asset->description; ?><br/>
                  <strong>Filename: </strong><?php echo $asset->filename; ?><br/>
                  <strong>File Size: </strong><?php echo FileHelper::formatBytes($asset->filesize); ?><br/>
                  <strong>Dimensions: </strong><?php echo $asset->height . 'x' . $asset->width; ?>
                </p>

                <a class="btn btn-primary btn-sm"
                   href="<?php echo Route::_('index.php?option=com_jsports&view=sponsorasset&layout=edit&id=' . $asset->id . '&return=' . $return); ?>">
                  Edit
                </a>

                <a class="btn btn-danger btn-sm"
                   onClick="return confirm('Are you sure?');"
                   href="<?php echo Route::_('index.php?option=com_jsports&task=sponsorasset.delete&id=' . $asset->id . '&return=' . $return); ?>">
                  Delete
                </a>
              </div><!-- /.card-body -->
            </div><!-- /.card -->
          </div><!-- /.col -->
        <?php endforeach; ?>
      </div><!-- /.row row-cols-* -->

    <?php endif; ?>

  </div><!-- /.col-md-12 -->
</div><!-- /.row -->

<?php echo HTMLHelper::_('uitab.endTab'); ?>
		
		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

