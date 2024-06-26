<?php
/**
 * @package     RSGallery2
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2017-2024 RSGallery2 Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * RSGallery is Free Software
 */

defined('_JEXEC') or die;

?>
<script language="javascript" type="text/javascript">
	<!--
	function Joomla.submitbutton3(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_directory.value == "") {
			alert("<?php echo JText::_('COM_RSGALLERY2_PLEASE_SELECT_A_DIR', true); ?>");
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	};

	function Joomla.submitbutton4(pressbutton) {
		var form = document.adminForm;

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://") {
			alert("<?php echo JText::_('COM_RSGALLERY2_PLEASE_ENTER_A_URL', true); ?>");
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	};
	//-->
</script>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">

    <br>
    <legend>Sorry, error in implementation. Functions below are not working any more  :-(</legend>
    <strong>detected in Jan 2019</strong>
    <br>
    Sidebar links do not start, Buttons not checked
    <br>
    <br>
    <br>


	<?php
	HTML_RSGALLERY::RSGallerySidebar();
	?>

	<?php if ($this->ftp) : ?>
		<?php echo $this->loadTemplate('ftp'); ?>
	<?php endif; ?>

	<table class="adminform">
		<tr>
			<th colspan="2"><?php echo JText::_('COM_RSGALLERY2_UPLOAD_PACKAGE_FILE'); ?></th>
		</tr>
		<tr>
			<td width="120">
				<label for="install_package"><?php echo JText::_('COM_RSGALLERY2_PACKAGE_FILE'); ?>:</label>
			</td>
			<td>
				<input class="input_box" id="install_package" name="install_package" type="file" size="57" />
				<input class="button" type="button" value="<?php echo JText::_('COM_RSGALLERY2_UPLOAD_FILE'); ?> &amp; <?php echo JText::_('COM_RSGALLERY2_INSTALL'); ?>" onclick="Joomla.submitbutton()" />
			</td>
		</tr>
	</table>

	<table class="adminform">
		<tr>
			<th colspan="2"><?php echo JText::_('COM_RSGALLERY2_INSTALL_FROM_DIRECTORY'); ?></th>
		</tr>
		<tr>
			<td width="120">
				<label for="install_directory"><?php echo JText::_('COM_RSGALLERY2_INSTALL_DIRECTORY'); ?>:</label>
			</td>
			<td>
				<input type="text" id="install_directory" name="install_directory" class="input_box" size="70" value="<?php echo $this->state->get('install.directory'); ?>" />
				<input type="button" class="button" value="<?php echo JText::_('COM_RSGALLERY2_INSTALL'); ?>" onclick="Joomla.submitbutton3()" />
			</td>
		</tr>
	</table>

	<table class="adminform">
		<tr>
			<th colspan="2"><?php echo JText::_('COM_RSGALLERY2_INSTALL_FROM_URL'); ?></th>
		</tr>
		<tr>
			<td width="120">
				<label for="install_url"><?php echo JText::_('COM_RSGALLERY2_INSTALL_URL'); ?>:</label>
			</td>
			<td>
				<input type="text" id="install_url" name="install_url" class="input_box" size="70" value="http://" />
				<input type="button" class="button" value="<?php echo JText::_('COM_RSGALLERY2_INSTALL'); ?>" onclick="Joomla.submitbutton4()" />
			</td>
		</tr>
	</table>

	<input type="hidden" name="type" value="" />
	<input type="hidden" name="installtype" value="upload" />
	<input type="hidden" name="task" value="doInstall" />
	<input type="hidden" name="option" value="com_rsgallery2" />
	<input type="hidden" name="rsgOption" value="installer" />
	<?php echo JHtml::_('form.token'); ?>
</form>
