<?php
/**
 * Item class
 *
 * @version       $Id: video.php 1011 2011-01-26 15:36:02Z mirjam $
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
class rsgItem_video extends rsgItem
{
	/**
	 * rsgResource: display image of first frame of video for this item
	 */
	var $display = null;

	/**
	 * rsgResource: the original video
	 */
	var $original = null;

	/**
	 * @param mixed|null $type
	 * @param            $mimetype
	 * @param            $gallery
	 * @param            $row
	 * @since 4.3.0
     */
	function __construct($type, $mimetype, &$gallery, $row)
	{
		parent::__construct($type, $mimetype, $gallery, $row);

		$this->_determineResources();
	}

	/**
	 * @return the thumbnail
	 * @todo: we need to return video thumbnail
	 * @since 4.3.0
     */
	function thumb()
	{
		return $this->thumb;
	}

	/**
	 * @return the display image
	 * @since 4.3.0
     */
	function display()
	{
		return $this->display;
	}

	/**
	 * @return the original image
	 * @since 4.3.0
     */
	function original()
	{
		return $this->original;
	}
	/**
	
	
	 * @since 4.3.0
    */
	function _determineResources()
	{
		global $rsgConfig;

		require_once(JPATH_RSGALLERY2_ADMIN . '/includes/video.utils.php');

		$gallery_path = $this->gallery->getPath("/");

		$thumb    = $rsgConfig->get('imgPath_thumb') . $gallery_path . videoUtils::getImgNameThumb($this->name);
		$display  = $rsgConfig->get('imgPath_display') . $gallery_path . videoUtils::getImgNameDisplay($this->name);
		$original = $rsgConfig->get('imgPath_original') . $gallery_path . $this->name;

		if (!JFile::exists(JPATH_ROOT . $original))
		{
			$this->original = $display;
		}

		$this->thumb    = new rsgResource($thumb);
		$this->display  = new rsgResource($display);
		$this->original = new rsgResource($original);

	}
}
