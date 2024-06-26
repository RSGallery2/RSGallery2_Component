<?php
/**
 * category class
 *
 * @version       $Id: images.class.php 1019 2011-04-12 14:16:47Z mirjam $
 * @package       RSGallery2
 * @copyright (C) 2005-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *                RSGallery2 is Free Software
 */

// no direct access
defined('_JEXEC') or die();

/**
 * Image database table class
 *
 * @package RSGallery2
 * @author  Ronald Smit <ronald.smit@rsdev.nl>
 */
class rsgImagesItem extends JTable
{
	/** @var int Primary key */
	var $id = null;
	/** @var string */
	var $name = null;
	/** @var string */
	var $alias = null;
	/** @var string */
	var $descr = null;
	/** @var int */
	var $gallery_id = null;
	/** @var string */
	var $title = null;
	/** @var int */
	var $hits = null;
	/** @var datetime */
	var $date = null;
	/** @var int */
	var $rating = null;
	/** @var int */
	var $votes = null;
	/** @var int */
	var $comments = null;
	/** @var boolean */
	var $published = null;
	/** @var int */
	var $checked_out = null;
	/** @var datetime */
	var $checked_out_time = null;
	/** @var int */
	var $ordering = null;
	/** @var boolean */
	var $approved = null;
	/** @var int */
	var $userid = null;
	/** @var string in form text;value; ... */
	var $params = null;
	/** @var int */
	var $asset_id = null;

	/**
	 * @param JDatabaseDriver $db A database connector object
	 * @since 4.3.0
     */
    function __construct(&$db)
	{
		parent::__construct('#__rsgallery2_files', 'id', $db);
	}

	/** overloaded check function
	 *
	 * @return bool
	 * @since 4.3.0
     */
	function check()
	{
		// filter malicious code
		$ignoreList = array('params', 'descr');

		$ignore = is_array($ignoreList);

		$filter = &JFilterInput::getInstance();
		foreach ($this->getProperties() as $k => $v)
		{
			if ($ignore && in_array($k, $ignoreList))
			{
				continue;
			}
			$this->$k = $filter->clean($this->$k);
		}

		/** check for valid name */
		if (trim($this->title) == '')
		{
			// $this->_error = JText::_('COM_RSGALLERY2_PLEASE_PROVIDE_A_VALID_IMAGE_TITLE');
			$this->setError(JText::_('COM_RSGALLERY2_PLEASE_PROVIDE_A_VALID_IMAGE_TITLE'));

			return false;
		}

		return true;
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form `table_name.id`
	 * where id is the value of the primary key of the table.
	 *
	 * @return string
	 * @since 4.3.0
     */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_rsgallery2.item.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return      string
	 * @since 4.3.0
     */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the item (which is the asset id of the gallery)
	 *
	 * @param JTable $table
	 * @param int    $id
	 *
	 * @return int|null
	 * @since 4.3.0
     */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables
		$assetId = null;
		$db      = $this->getDbo();    //$this is the rsgImagesItem object

		// Build the query to get the asset id for the gallery to which this item belongs
		$query = $db->getQuery(true);
		$query->select('asset_id');
		$query->from('#__rsgallery2_galleries');
		$query->where('id = ' . (int) $this->gallery_id);

		// Get the asset id from the database.
		$db->setQuery($query);
		if ($result = $db->loadResult())
		{
			$assetId = (int) $result;
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
}

