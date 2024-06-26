<?php // no direct access
/**
 * @package       RSGallery2
 * @copyright (C) 2003-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * RSGallery is Free Software
 */

defined('_JEXEC') or die();


JHtml::_('bootstrap.tooltip');

global $Rsg2DebugActive;

global $rsgConfig;
$this->configVars = $rsgConfig;

?>

<div id="installer-install" class="clearfix">
	<?php if (!empty($this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

			<form action="<?php echo JRoute::_('index.php?option=com_rsgallery2&view=config&amp;layout=RawView'); ?>"
					method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

				<legend><?php echo JText::_('COM_RSGALLERY2_CONFIGURATION_RAW_VIEW'); ?></legend>
                <div></div><strong><?php echo JText::_('COM_RSGALLERY2_CONFIG_MINUS_VIEW_TXT'); ?></strong><br></div>

				<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'ConfigRawView')); ?>

				<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'ConfigRawView', JText::_('COM_RSGALLERY2_CONFIGURATION_RAW_VIEW', true)); ?>

				<?php

				echo '<pre>';
				// Old RSG2 config vars echo json_encode(get_object_vars($this->configVars), JSON_PRETTY_PRINT);
				echo json_encode($this->configVars, JSON_PRETTY_PRINT);
				echo '</pre>';
				?>

				<?php echo JHtml::_('bootstrap.endTab'); ?>

				<?php echo JHtml::_('bootstrap.endTabSet'); ?>

				<!--input type="hidden" name="option" value="com_rsgallery2" />
				<input type="hidden" name="rsgOption" value="maintenance" /-->

				<input type="hidden" name="task" value="">
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>
		<div id="loading"></div>
	</div>
</div>

