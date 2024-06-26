<?php
/**
 * Maintenance class for RSGallery2
 *
 * @version       $Id: maintenance.class.php 1037 2011-08-03 14:22:00Z mirjam $
 * @package       RSGallery2
 * @copyright (C) 2003-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *                RSGallery is Free Software
 */

defined('_JEXEC') or die();

/**
 * Maintenance class for RSGallery2
 *
 * @package RSGallery2
 * @author  Ronald Smit <ronald.smit@rsdev.nl>
 */
class rsg2_maintenance
{
	/**
	
	 * @since 4.3.0
    */
	function __construct()
	{

	}

	/**
	 * Samples a random thumb from the specified gallery and compares dimensions against Config settings
	 *
	 * @param Int $gid Gallery ID
	 *
	 * @return bool True if size has changed, false if not.
	 * @since 4.3.0
     */
	static function thumbSizeChanged($gid)
	{
		global $rsgConfig;
		$gallery = rsgGalleryManager::_get($gid);
		$images  = $gallery->items();
		foreach ($images as $image)
		{
			$imgname[] = $image->name;
		}
		$image = array_rand($imgname);

		//$imgdata = getimagesize( imgUtils::getImgThumbPath($imgname[$image], true) );
		$imgdata = getimagesize(imgUtils::getImgThumb($imgname[$image], true));
		if ($imgdata[0] == $rsgConfig->get('thumb_width'))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Samples a random display image from the specified gallery and compares dimensions against Config settings
	 *
	 * @param int $gid Gallery ID
	 *
	 * @return bool True if size has changed, false if not.
	 * @since 4.3.0
     */
	static function displaySizeChanged($gid)
	{
		global $rsgConfig;
		$gallery = rsgGalleryManager::_get($gid);
		$images  = $gallery->items();
		foreach ($images as $image)
		{
			$imgname[] = $image->name;
		}
		$image = array_rand($imgname);

		//$imgdata = getimagesize( imgUtils::getImgDisplayPath($imgname[$image], true) );
		$imgdata = getimagesize(imgUtils::getImgDisplay($imgname[$image], true));
		if ($imgdata[0] == $rsgConfig->get('image_width'))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}

/**
 * Class rsg2_consolidate
 */
class rsg2_consolidate extends rsg2_maintenance
{

	/**
	 *
	 * @since 4.3.0
     */
	static function consolidateDB()
	{
		global $rsgConfig;
		$database = JFactory::getDBO();
		//Load all image names from DB in array
		$sql = "SELECT name FROM #__rsgallery2_files";
		$database->setQuery($sql);
		$names_db = rsg2_consolidate::arrayToLower($database->loadColumn());

		$files_display  = rsg2_consolidate::getFilenameArray($rsgConfig->get('imgPath_display'));
		$files_original = rsg2_consolidate::getFilenameArray($rsgConfig->get('imgPath_original'));
		$files_thumb    = rsg2_consolidate::getFilenameArray($rsgConfig->get('imgPath_thumb'));
		$files_total    = array_unique(array_merge($files_display, $files_original, $files_thumb));

		html_rsg2_maintenance::consolidateDB($names_db, $files_display, $files_original, $files_thumb, $files_total);
	}

	/**
	 * Fills an array with the file names, found in the specified directory
	 *
	 * @param string $dir Directory from Joomla root
	 *
	 * @return array Array with file names
	 * @since 4.3.0
     */
	static function getFilenameArray($dir)
	{
		global $rsgConfig;

		//Load all image names from filesystem in array
		$dh = opendir(JPATH_ROOT . $dir);
		//Files to exclude from the check

		$exclude  = array('.', '..', 'Thumbs.db', 'thumbs.db');
		$allowed  = array('jpg', 'gif');
		$names_fs = array();

		while (false !== ($filename = readdir($dh)))
		{
			$ext = explode(".", $filename);
			$ext = array_reverse($ext);
			$ext = strtolower($ext[0]);
			if (!is_dir(JPATH_ROOT . $dir . "/" . $filename) AND !in_array($filename, $exclude) AND in_array($ext, $allowed))
			{
				if ($dir == $rsgConfig->get('imgPath_display') OR $dir == $rsgConfig->get('imgPath_thumb'))
				{
					//Recreate normal filename, eliminating the extra ".jpg"
					$names_fs[] = substr(strtolower($filename), 0, -4);
				}
				else
				{
					$names_fs[] = strtolower($filename);
				}
			}
			else
			{
				//Do nothing
				continue;
			}
		}
		closedir($dh);

		return $names_fs;

	}

	/**
	 * Changes all values of an array to lowercase
	 *
	 * @param array $array mixed case mixed or upper case values
	 *
	 * @return array lower case values
	 * @since 4.3.0
     */
	static function arrayToLower($array)
	{
		$array = explode("|", strtolower(implode("|", $array)));

		return $array;
	}
}

