<?php
/**
 * @package     RSGallery2
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2016 - 2017 RSGallery2
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * RSGallery is Free Software
 */

// No direct access to this file
defined('_JEXEC') or die;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 *
 *
 * @since 4.3.0
 */
class rsgallery2ModelMaintDatabaseTables extends JModelList
{
//    protected $text_prefix = 'COM_RSG2';

//    protected function removeImageReferences ()
	public function removeDataInTables()
	{
		$msg = "removeDataInTables: ";

//COM_RSGALLERY2_DELETE_FROM_FILESYSTEM COM_RSGALLERY2_DELETE_IMAGES
		$msg = $msg . $this->PurgeTable('#__rsgallery2_acl', JText::_('COM_RSGALLERY2_PURGED_TABLE_RSGALLERY2_ACL')) . '<br>';
		$msg = $msg . $this->PurgeTable('#__rsgallery2_files', JText::_('COM_RSGALLERY2_PURGED_IMAGE_ENTRIES_FROM_DATABASE')) . '<br>';
		// $msg = $msg . $this->PurgeTable ('#__rsgallery2_cats', JText::_('COM_RSGALLERY2_PURGED_TABLE_RSGALLERY2_CATS')) . '<br>';
		$msg = $msg . $this->PurgeTable('#__rsgallery2_galleries', JText::_('COM_RSGALLERY2_PURGED_GALLERIES_FROM_DATABASE')) . '<br>';
		$msg = $msg . $this->PurgeTable('#__rsgallery2_config', JText::_('COM_RSGALLERY2_PURGED_TABLE_RSGALLERY2_CONFIG')) . '<br>';
		$msg = $msg . $this->PurgeTable('#__rsgallery2_comments', JText::_('COM_RSGALLERY2_PURGED_TABLE_RSGALLERY2_COMMENTS')) . '<br>';

		return $msg;
	}

	/**
	 * Deletes all Tables of RSG2 in preparation of of uninstall/reinstall
	 *
	 * This deletion (dropping) leads to an unwanted effect:
	 * The uninstall part of joomla can't be opened as the tables are missing
	 * ToDo: Better would be to remove the comments before the drop commands in  the file uninstall.mysql.utf8.sql
	 *
	 * @return string $msg
	 */
	public function removeAllTables()
	{
		$msg = "RemoveAllTables: ";

		$msg = $msg . $this->DropTable('#__rsgallery2_acl', JText::_('COM_RSGALLERY2_DROPED_TABLE___RSGALLERY2_ACL')) . '<br>';
		$msg = $msg . $this->DropTable('#__rsgallery2_files', JText::_('COM_RSG2DROPED_TABLE___RSGALLERY2_FILES')) . '<br>';
		//$msg = $msg . $this->DropTable ('#__rsgallery2_cats', JText::_('COM_RSGALLERY2_DROPED_TABLE___RSGALLERY2_CATS')) . '<br>';
		$msg = $msg . $this->DropTable('#__rsgallery2_galleries', JText::_('COM_RSG2DROPED_TABLE___RSGALLERY2_GALLERIES')) . '<br>';
		$msg = $msg . $this->DropTable('#__rsgallery2_config', JText::_('COM_RSG2DROPED_TABLE___RSGALLERY2_CONFIG')) . '<br>';
		$msg = $msg . $this->DropTable('#__rsgallery2_comments', JText::_('COM_RSG2DROPED_TABLE___RSGALLERY2_COMMENTS')) . '<br>';

		return $msg;
	}

	/**
	 * Removes one table from RSG2
	 *
	 * @param string $TableId
	 * @param string $successMsg
	 *
	 * @return string bool success or error message
	 */
	private function PurgeTable($TableId, $successMsg)
	{
		try
		{
			$db = JFactory::getDbo();
			$db->truncateTable($TableId);
			$db->execute();

			$msg = $successMsg;
		}
		catch (Exception $e)
		{
			$msg = 'Purge table failure: "' . $TableId . '" ' . $e->getCode() . ':' . $e->getMessage() . '\n';
		}

		return $msg;
	}

	/**
	 * Removes one table from RSG2
	 *
	 * @param string $TableId
	 * @param string $successMsg
	 *
	 * @return string bool success or error message
	 */
	private function DropTable($TableId, $successMsg)
	{
		try
		{
			$db = JFactory::getDbo();
			$db->dropTable($TableId, true);
			$db->execute();

			$msg = $successMsg;
		}
		catch (Exception $e)
		{
			$msg = 'Drop table failure: "' . $TableId . '" ' . $e->getCode() . ':' . $e->getMessage() . '\n';
		}

		return $msg;
	}
}
