<?php 
/**
 * @package RSGallery2
 * @copyright (C) 2003 - 2016 RSGallery2
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * RSGallery is Free Software
 */

defined( '_JEXEC' ) or die();

JHtml::_('behavior.tooltip');
// ToDo: Activate tooltips on every button

global $Rsg2DebugActive;

// public static $extension = 'COM_RSG2';

$doc = JFactory::getDocument();
$doc->addStyleSheet (JURI::root(true)."/administrator/components/com_rsgallery2/css/Maintenance.css");

// Purge / delete of database variables should be confirmed 
$script = "
	jQuery(document).ready(function($){ 
/*		$('.consolidateDB').on('click', function () {
			return confirm('" . JText::_('COM_RSGALLERY2_CONFIRM_CONSIDER_BACKUP_OR_CONTINUE') . "'); 
		}); 
*/
/*
		$('.regenerateThumbs').on('click', function () { 
			return confirm('" . JText::_('COM_RSGALLERY2_CONFIRM_CONSIDER_BACKUP_OR_CONTINUE') . "'); 
		}); 
*/
/*		$('.optimizeDB').on('click', function () { 
			return confirm('" . JText::_('COM_RSGALLERY2_CONFIRM_CONSIDER_BACKUP_OR_CONTINUE') . "'); 
		}); 
*/
/*		$('.editConfigRaw').on('click', function () {
			return confirm('" . JText::_('COM_RSGALLERY2_CONFIRM_CONSIDER_BACKUP_OR_CONTINUE') . "'); 
		}); 
*/
		$('.purgeImagesAndData').on('click', function () {
			return confirm('" . JText::_('COM_RSGALLERY2_CONFIRM_CONSIDER_BACKUP_OR_CONTINUE') . "'); 
		}); 

		$('.uninstallDataTables').on('click', function () {
			return confirm('" . JText::_('COM_RSGALLERY2_CONFIRM_CONSIDER_BACKUP_OR_CONTINUE')  . "'); 
		}); 
	}); 
"; 
$doc->addScriptDeclaration($script); 


 /**
  * Used to generate buttons
  * @param string $link URL for button link
  * @param string $image Image name for button image
  * @param string $title Command title
  * @param string $text Command explaining text
  * @param string $addClass
  */
function quickiconBar( $link, $image, $title, $text = "", $addClass = '' ) {
    ?>
		<div class="rsg2-icon-bar">
			<a href="<?php echo $link; ?>" class="<?php echo $addClass; ?>" >
				<figure class="rsg2-icon">
					<?php echo JHtml::image('administrator/components/com_rsgallery2/images/'.$image, $text); ?>
					<figcaption class="rsg2-text">
						<span class="maint-title"><?php echo $title;?></span>
						<!--br-->
						<span class="maint-text"><?php echo $text;?></span>
					</figcaption>
				</figure>
			</a>
		</div>
<?php
}

/**
 * Used to generate buttons with icomoon icon
 * @param string $link URL for button link
 * @param string $imageClass Image name for button image
 * @param string $title Command title
 * @param string $text Command explaining text
 * @param string $addClass
 */
function quickIconMoonBar( $link, $imageClass, $title, $text = "", $addClass = '' ) {
	?>
	<div class="rsg2-icon-bar">
		<a href="<?php echo $link; ?>" class="<?php echo $addClass; ?>" >
			<figure class="rsg2-icon">
				<span class="<?php echo $imageClass ?>" style="font-size:40px;"></span>
				<figcaption class="rsg2-text">
					<span class="maint-title"><?php echo $title;?></span>
					<!--br-->
					<span class="maint-text"><?php echo $text;?></span>
				</figcaption>
			</figure>
		</a>
	</div>
	<?php
}

/**
 * Used to generate buttons with two icomoon icon
 * @param string $link URL for button link
 * @param string $imageClass Image name for button image
 * @param string $title Command title
 * @param string $text Command explaining text
 * @param string $addClass
 */
function quickTwoIconMoonBar( $link, $imageClass1, $imageClass2, $title, $text = "", $addClass = '' ) {
	?>
	<div class="rsg2-icon-bar">
		<a href="<?php echo $link; ?>" class="<?php echo $addClass; ?>" >
			<figure class="rsg2-icon">
				<span class="<?php echo $imageClass1 ?> iconMoon01" style="font-size:30px;"></span>
				<span class="<?php echo $imageClass2 ?> iconMoon02" style="font-size:30px;"></span>
				<figcaption class="rsg2-text">
					<span class="maint-title"><?php echo $title;?></span>
					<!--br-->
					<span class="maint-text"><?php echo $text;?></span>
				</figcaption>
			</figure>
		</a>
	</div>
	<?php
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_rsgallery2&view=maintenance'); ?>"
      method="post" name="adminForm" id="adminForm">

<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>

        <div class="row-fluid grey-background">
            <div class="container-fluid grey-background">
				<div class="row span4 rsg2-container-icon-set">
					<div class="icons-panel repair">
						<div class="row-fluid">
							<div class="icons-panel-title repairZone">
								<h3>
									<?php echo JText::_('COM_RSGALLERY2_REPAIR_ZONE');?>
								</h3>
							</div>
							<div class='icons-panel-info'>
								<strong>
									<?php echo JText::_('COM_RSGALLERY2_FUNCTIONS_MAY_CHANGE_DATA');?>
								</strong>
							</div>
							<?php
							$link = 'index.php?option=com_rsgallery2&amp;view=config&amp;layout=RawView';
							quickTwoIconMoonBar ($link, 'icon-equalizer', 'icon-eye',
								JText::_('COM_RSGALLERY2_CONFIGURATION_VARIABLES'),
								JText::_('COM_RSGALLERY2_CONFIG_MINUS_VIEW_TXT').'                        ',
								'viewConfigRaw');
							?>

							<?php
							if($this->UserIsRoot ) {
							?>

								<?php
								//$link = 'index.php?option=com_rsgallery2&amp;task=maintenance.consolidateDB';
								$link = 'index.php?option=com_rsgallery2&amp;rsgOption=maintenance&amp;task=consolidateDB';
								quickiconBar($link, 'blockdevice.png',
									JText::_('COM_RSGALLERY2_MAINT_CONSOLDB'),
									JText::_('COM_RSGALLERY2_MAINT_CONSOLDB_TXT'),
									'consolidateDB');
								?>

								<?php
								$link =  'index.php?option=com_rsgallery2&amp;view=maintRegenerateImages';
								quickTwoIconMoonBar ($link, 'icon-image', 'icon-wand',
									JText::_('COM_RSGALLERY2_MAINT_REGEN_BUTTON_DISPLAY'),
									JText::_('COM_RSGALLERY2_MAINT_REGEN_TXT').'                        ',
									'regenerateThumbs');
								?>

								<?php
								$link = 'index.php?option=com_rsgallery2&amp;task=maintSql.optimizeDB';
								quickTwoIconMoonBar ($link, 'icon-database', 'icon-clock', // 'icon-checkbox-checked'
									JText::_('COM_RSGALLERY2_MAINT_OPTDB'),
									JText::_('COM_RSGALLERY2_MAINT_OPTDB_TXT'),
									'optimizeDB');
								?>
							<?php
							}
							?>

							<?php
							$link = 'index.php?option=com_rsgallery2&rsgOption=installer';

							quickIconMoonBar( $link, 'icon-scissors clsTemplate',
								JText::_('COM_RSGALLERY2_TEMPLATE_MANAGER'),
								JText::_('COM_RSGALLERY2_TEMPLATE_EXPLANATION'),
								'templateManager');
							?>

						</div>
					</div>
				</div>
									
				<div class="row span4 rsg2_container_icon_set">
					<div class="icons-panel danger">
						<div class="row-fluid">
							<div class="icons-panel-title dangerZone">
								<h3>
									<?php echo JText::_('COM_RSGALLERY2_DANGER_ZONE');?>
								</h3>
							</div>
							<?php
								if( $this->debugActive ) {
							?>
									<div class='icons-panel-info'>
										<strong>
											<?php echo JText::_('COM_RSGALLERY2_ONLY_WHEN_YOU_KNOW_WHAT_YOU_ARE_DOING'); ?>
										</strong>
									</div>

									<?php
									$link = 'index.php?option=com_rsgallery2&amp;view=config&amp;layout=RawEdit';
									quickTwoIconMoonBar ($link, 'icon-equalizer', 'icon-edit',
										JText::_('COM_RSGALLERY2_CONFIGURATION_RAW_EDIT'),
										JText::_('COM_RSGALLERY2_CONFIG_MINUS_RAW_EDIT_TXT'),
										'editConfigRaw');
									?>

									<?php
									$link = 'index.php?option=com_rsgallery2&amp;task=MaintCleanUp.purgeImagesAndData';
									//$link = 'index.php?option=com_rsgallery2&task=purgeEverything';
									quickTwoIconMoonBar ($link, 'icon-database ', 'icon-purge',
										JText::_('COM_RSGALLERY2_PURGEDELETE_EVERYTHING'),
										JText::_('COM_RSGALLERY2_PURGEDELETE_EVERYTHING_TXT'),
										'purgeImagesAndData');
									?>
									<?php
									$link = 'index.php?option=com_rsgallery2&amp;task=MaintCleanUp.removeImagesAndData';
									//$link = 'index.php?option=com_rsgallery2&task=reallyUninstall';
									quickTwoIconMoonBar ($link, 'icon-database ', 'icon-delete',
										JText::_('COM_RSGALLERY2_C_REALLY_UNINSTALL'),
										'<del>' . JText::_('COM_RSGALLERY2_C_REALLY_UNINSTALL_TXT') . '</del><br>'
										. JText::_('COM_RSGALLERY2_C_TODO_UNINSTALL_TXT'),
										'uninstallDataTables');
									?>
									<?php
										//} else {
										//	echo JText::_('COM_RSGALLERY2_MORE_FUNCTIONS_WITH_DEBUG_ON');
										//}
									?>
							<?php
								}
							?>

						</div>
					</div>
                </div>
		<!--
			</div>
		</div>

		<div class="row-fluid grey-background">
			<div class="container-fluid grey-background">
		-->
				<?php
				if( $this->upgradeActive ) {
				?>
				<div class="row span4 rsg2_container_icon_set">
					<div class="icons-panel upgrade">
						<div class="row-fluid">
							<div class="icons-panel-title upgradeZone">
								<h3>
									<?php echo JText::_('COM_RSGALLERY2_UPGRADE_ZONE');?>
								</h3>
							</div>

							<div class='icons-panel-info'>
								<strong>
									<?php echo JText::_('COM_RSGALLERY2_UPGRADE_ZONE_DESCRIPTION');?>
								</strong>
							</div>

							<?php
							$link = 'index.php?option=com_rsgallery2&amp;task=maintSql.createGalleryAccessField';
							quickTwoIconMoonBar ($link, 'icon-database', 'icon-wrench',
								JText::_('COM_RSGALLERY2_CREATE_GALLERY_ACCESS_FIELD'),
								JText::_('COM_RSGALLERY2_CREATE_GALLERY_ACCESS_FIELD_DESCRIPTION'),
								'createGalleryAccessField');
							?>

							<?php
							$link = 'index.php?option=com_rsgallery2&amp;task=maintSql.completeSqlTables';
							quickTwoIconMoonBar ($link, 'icon-database', 'icon-book',
								JText::_('COM_RSGALLERY2_COMPLETE_SQL_TABLES'),
								JText::_('COM_RSGALLERY2_COMPLETE_SQL_TABLES_DESC'),
								'compareDb2SqlFile');
							?>

						</div>
					</div>
				</div>
				<?php
					}
				?>


				<?php
				if( $this->testActive ) {
				?>
				<div class="row span4 rsg2_container_icon_set">
					<div class="icons-panel test">
						<div class="row-fluid">
							<div class="icons-panel-title testZone">
								<h3>
									<?php echo JText::_('COM_RSGALLERY2_TEST_ZONE');?>
								</h3>
							</div>

							<div class='icons-panel-info'>
								<strong>
									<?php echo JText::_('COM_RSGALLERY2_TEST_ZONE_DESCRIPTION');?>
								</strong>
							</div>

							<?php
							$link = 'index.php?option=com_rsgallery2&amp;task=maintenance.compareDb2SqlFile';
							//$link = 'index.php?option=com_rsgallery2&amp;rsgOption=maintenance&amp;task=CompareDb2SqlFile';
							//$link = 'index.php?option=com_rsgallery2&amp;task=compareDb2SqlFile';
							quickTwoIconMoonBar ($link, 'icon-database', 'icon-book',
								JText::_('COM_RSGALLERY2_COMPARE_DB_TO_SQL_FILE'),
								JText::_('COM_RSGALLERY2_COMPARE_DB_TO_SQL_DESC'),
								'compareDb2SqlFile');
							?>

						</div>
					</div>
				</div>
				<?php
					}
				?>
				<!--

			</div>
		</div>

		<div class="row-fluid grey-background">
			<div class="container-fluid grey-background">
				-->
				<?php
					if( $this->developActive ) {
				?>
				<div class="row span4 rsg2_container_icon_set">
						<div class="icons-panel developer">
							<div class="row-fluid">
								<div class="icons-panel-title developerZone">
									<h3>
										<?php echo JText::_('COM_RSGALLERY2_DEVELOPER_ZONE');?>
									</h3>
								</div>
								<div class='icons-panel-info'>
									<strong>
										<?php echo JText::_('COM_RSGALLERY2_ONLY_WHEN_YOU_KNOW_WHAT_YOU_ARE_DOING'); ?>
									</strong>
								</div>

								<?php
								// $link = 'index.php?option=com_rsgallery2&amp;task=maintenance.consolidateDB';
								$link = 'index.php?option=com_rsgallery2&amp;view=maintConsolidateDB';
								quickTwoIconMoonBar ($link, 'icon-database', 'icon-checkbox-checked',
									JText::_('COM_RSGALLERY2_MAINT_CONSOLIDATE_IMAGE_DATABASE'),
									JText::_('COM_RSGALLERY2_MAINT_CONSOLDB_TXT'),
									'consolidateDB');
								?>

								<?php
								$link = 'index.php?option=com_rsgallery2&amp;view=config';
								quickTwoIconMoonBar ($link, 'icon-equalizer', 'icon-cog',
									JText::_('COM_RSGALLERY2_CONFIGURATION'),
									JText::_('COM_RSGALLERY2_CONFIG_MINUS_VIEW_TXT').'                        ',
									'tempStandardconfigEdit');
								?>

								<?php
								$link = 'index.php?option=com_rsgallery2&task=config_rawEdit';
								quickiconBar($link, 'menu.png',
									JText::_('COM_RSGALLERY2_CONFIG_MINUS_RAW_EDIT'),
									JText::_('COM_RSGALLERY2_CONFIG_MINUS_RAW_EDIT_TXT'),
									'editConfigRaw');
								?>

							</div>
						</div>
					</div>
				<?php
					}
				?>
			</div>
		</div>

        <div>
			<input type="hidden" name="option" value="com_rsgallery2" />
			<input type="hidden" name="rsgOption" value="maintenance" />

            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </div>
</form>

