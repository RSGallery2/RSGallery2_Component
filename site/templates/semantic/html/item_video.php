<?php
/**
 * @package     RSGallery2
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2017-2024 RSGallery2 Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * RSGallery is Free Software
 */
?>
<?php
defined('_JEXEC') or die();
JHTML::_('behavior.framework', true);  // ToDo: Remove mootools
global $rsgConfig;

$item = $this->currentItem;

//$templatePath = JURI_SITE . "components/com_rsgallery2/templates/". 
$input       = JFactory::getApplication()->input;
$PreTemplate = $input->get('rsgTemplate', $rsgConfig->get('template'), 'CMD');

$templatePath = JURI_SITE . "components/com_rsgallery2/templates/" . $PreTemplate;

$jsSwf = '
		window.addEvent("domready", function() {
		var flashvars = {movie:"' . $item->display->url() . '",
		fgcolor: "0x000000",
		bgcolor: "0x000000",
		autoload: "on",
		autorewind: "on",
		volume: "70"}; 
		swfobject.embedSWF("' . JURI_SITE . '/components/com_rsgallery2/flash/player.swf",
		"rsg2-flashMovie", 
		"320", "240", 
		"7", 
		"' . JURI_SITE . '/components/com_rsgallery2/flash/expressInstall.swf",
		flashvars,
		{ wmode: "transparent", loop:false, autoPlay:true }
		);
		});';

$doc = JFactory::getDocument();
$doc->addScriptDeclaration($jsSwf);
$doc->addScript(JURI_SITE . '/components/com_rsgallery2/flash/script/swfobject.js');

?>
	<div id="rsg2-flashMovie"><p><?php echo JText::_('COM_RSGALLERY2_THE_MOVIE_SHOULD_APPEAR_HERE'); ?></p></div><?php

?>