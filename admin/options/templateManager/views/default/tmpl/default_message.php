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
<?php
$state    = $this->get('State');
$message1 = $state->get('message');
$message2 = $state->get('extension.message');
?>
<table class="adminform">
	<tbody>
	<?php if ($message1) : ?>
		<tr>
			<th><?php echo JText::_($message1) ?></th>
		</tr>
	<?php endif; ?>
	<?php if ($message2) : ?>
		<tr>
			<td><?php echo $message2; ?></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
