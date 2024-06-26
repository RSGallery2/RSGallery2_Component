<?php
/**
 * Item class
 *
 * @version       $Id: item.php 1088 2012-07-05 19:28:28Z mirjam $
 * @package       RSGallery2
 * @copyright (C) 2005-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *                RSGallery2 is Free Software
 */

defined('_JEXEC') or die();

/**
 * The generic item class
 *
 * @package RSGallery2
 * @author  Jonah Braun <Jonah@WhaleHosting.ca>
 */
class rsgItem extends JObject
{
	/**
	 * the general content type
	 */
	var $type = null;

	/**
	 * the full mimetype
	 */
	var $mimetype = null;

	/**
	 * the parent gallery
	 */
	var $gallery = null;

	/**
	 * rsgResource: thumbnail for this item
	 */
	var $thumb = null;

	/**
	 * JRegistry object representing the params
	 */
	var $parameters = null;

	/**
	 * @param array a database row
	 */
	/**
	 * @param mixed|null $type
	 * @param            $mimetype
	 * @param            $gallery
	 * @param            $row
	 * @since 4.3.0
     */
	function __construct($type, $mimetype, &$gallery, $row)
	{
		$this->type     = $type;
		$this->mimetype = $mimetype;
		$this->gallery  =& $gallery;

		foreach ($row as $n => $a)
			$this->$n = $a;
	}

	/**
	 * @return JRegistry|null (J Parameter)
	 * @since 4.3.0
     */
	function parameters()
	{
		if ($this->parameters === null)
		{
			// $this->parameters = new J Parameter($this->params);
			$registry = new JRegistry;
			$registry->loadString($this->params);
			$this->parameters = $registry->toArray();
		}

		return $this->parameters;
	}

	/**
	 * increases the hit counter for this object
	 *
	 * @return bool
	 * @since 4.3.0
     */
	function hit()
	{
		$query = 'UPDATE `#__rsgallery2_files` SET `hits` = hits + 1 WHERE `id` = ' . (int) $this->id;

		$database = JFactory::getDBO();
		$database->setQuery($query);

		if (!$database->execute())
		{
			// ToDo: 150130 $database->getErrorMsg deprecated
			// ToDo: 150130 setError deprecated
			$this->setError($database->getErrorMsg());

			return false;
		}

		$this->hits++;

		return true;
	}

	/**
	 * move the item to a different gallery
	 *
	 * @param int id of the target gallery
	 *
	 * @return bool true if succesfull
	 * @since 4.3.0
     */
	function move($target_gallery)
	{

		if ($target_gallery == null)
		{
			return false;
		}

		$query = 'UPDATE `#__rsgallery2_files` SET `gallery_id` = ' . (int) $target_gallery . ' WHERE `id` = ' . (int) $this->id;

		global $database, $rsgConfig;
		$database->setQuery($query);

		if (!$database->execute())
		{
			$this->setError($database->getErrorMsg());

			return false;
		}

		if ($rsgConfig->get('gallery_folders'))
		{

			// TODO: move files from source to target gallery folder

		}

		return true;

	}

	/**
	 * copy the item to an other gallery
	 *
	 * @param int id of the target gallery
	 *
	 * @return rsgItem newly created rsgItem
	 * @since 4.3.0
     */
	function copy($target_gallery)
	{

		if ($target_gallery == null)
		{
			return null;
		}

		global $database, $rsgConfig;

		$new_item             = clone($this);
		$new_item->gallery_id = $target_gallery;

		if (!$database->insertObject('#__rsgallery2_files', $new_item, 'id'))
		{
			$this->setError($database->getErrorMsg());

			return null;
		}

		if ($rsgConfig->get('gallery_folders'))
		{

			// TODO: copy files from source to target gallery folder

		}

		return $this;

	}

	/**
	 * remove the item from the gallery
	 *
	 * @param $target_gallery
	 *
	 * @return bool true if succesfull
	 * @since 4.3.0
     */
	function remove($target_gallery)
	{

		if ($target_gallery == null)
		{
			return false;
		}

		global $database, $rsgConfig;

		$query = 'DELETE `#__rsgallery2_files` WHERE `id` = ' . (int) $this->id;
		$database->setQuery($query);

		if (!$database->execute())
		{
			$this->setError($database->getErrorMsg());

			return false;
		}

		$query = 'DELETE `#__rsgallery2_comments` WHERE `id` = ' . (int) $this->id;
		$database->setQuery($query);
		$database->execute();

		if ($rsgConfig->get('gallery_folders'))
		{

			// TODO: remove files from gallery folder

		}

		// clear important data
		$this->gallery = null;
		$this->id      = null;

		return true;

	}

	/**
	 * save items data to datastore
	 *
	 * @return bool true if succesfull
	 * @since 4.3.0
     */
	function save()
	{

		$database = JFactory::getDBO();

		if (!$database->updateObject('#__rsgallery2_files', $this, 'id'))
		{
			// ToDo:: 150130 $database->getErrorMsg is deprecated
			$this->setError($database->getErrorMsg());

			return false;
		}

		return true;
	}

	/**
	 * static class returns the appropriate object for the item
	 *
	 * @param rsgGallery of the parent gallery
	 * @param array      of the database row
	 *
	 * @return the apropriate item object
	 * @since 4.3.0
     */
	static function getCorrectItemObject(&$gallery, $row)
	{
		// get mime type of file
		$mimeType = MimeTypes::getMimeType($row['name']);

		// get only the general content type
		$type = explode('/', $mimeType);
		$type = $type[0];

		if (file_exists(JPATH_RSGALLERY2_ADMIN . '/includes/items/' . $type . '.php'))
		{
			require_once(JPATH_RSGALLERY2_ADMIN . '/includes/items/' . $type . '.php');
			$itemClass = "rsgItem_$type";

			return new $itemClass($type, $mimeType, $gallery, $row);
		}
		else
		{
			$itemClass = "rsgItem";

			return new $itemClass($type, $mimeType, $gallery, $row);
		}
	}
}

/**
 * This class represents a file
 *
 * @package RSGallery2
 * @author  Jonah Braun <Jonah@WhaleHosting.ca>
 */
class rsgResource extends JObject
{
	/**
	 * the unique name to retrieve this resource
	 */
	var $name = null;

	/**
	 * @param mixed|null $name
	 * @since 4.3.0
     */
	function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * @return working URL to the resource
	 * @since 4.3.0
     */
	function url()
	{
		$url = JURI_SITE . trim($this->name, "/");

		return $url;
	}

	/**
	 * @return the mime type of the file
	 * @since 4.3.0
     */
	function mimeType()
	{
	}

	/**
	 * @return the absolute local file path
	 * @since 4.3.0
     */
	function filePath()
	{
		return JPATH_ROOT. '/' .$this->name;
	}

	/**
	 * copies file to resources location
	 *
	 * @param string path of file
	 *
	 * @return bool true on success
	 * @since 4.3.0
     **/
	function store($path)
	{

		if (!file_exists($path))
		{
			return false;
		}

		if (!file_exists($this->filePath))
		{

			if (!unlink($this->filePath))
			{
				return false;
			}

			if (!copy($path, $this->filePath))
			{
				return false;
			}

		}

		return true;

	}

	/**
	 * deletes resource from locale file system
	 *
	 * @return bool true if success
	 * @since 4.3.0
     **/
	function delete()
	{

		if (!unlink($this->filePath))
		{
			return false;
		}

		return true;

	}
}
