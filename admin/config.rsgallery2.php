<?php
/**
 * This file handles configuration processing for RSGallery.
 *
 * @version       $Id: config.rsgallery2.php 1085 2012-06-24 13:44:29Z mirjam $
 * @package       RSGallery2
 * @copyright (C) 2003-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * RSGallery is Free Software
 **/

defined('_JEXEC') or die();

/**
 * Class with util functions for RSGallery2
 *
 * @package RSGallery2
 * @since 4.3.0
 */
class galleryUtils
{

	/**
	 * Show gallery select list according to the permissions of the logged in user
	 *
	 * @param string  $action         Action type (permission)
	 * @param string  $select_name    Name of the select box, defaults to 'catid'
	 * @param integer $gallery_id     ID of selected gallery
	 * @param string  $js             Additional select tag attributes
	 * @param bool    $showTopGallery show Top Gallery to select, default no
	 *
	 * @since 4.3.0
	 */
	static function showUserGalSelectList($action = '', $select_name = 'catid', $gallery_id = null,
		$js = '', $showTopGallery = false)
	{
		$user = JFactory::getUser();

		//Get gallery Id's where action is permitted and write to string
		$galleriesAllowed = galleryUtils::getAuthorisedGalleries($action);
		$dropDown_html    = '<select name="' . $select_name . '" ' . $js . '><option value="-1" selected="selected" >' . JText::_('COM_RSGALLERY2_SELECT_GALLERY_FROM_LIST') . '</option>';

		if ($showTopGallery)
		{
			$dropDown_html .= "<option value=0";
			// Disable when action not allowed or user not owner
			if (!$user->authorise($action, 'com_rsgallery2'))
			{
				$dropDown_html .= ' disabled="disabled"';
			}
			if ($gallery_id == 0)
			{
				$dropDown_html .= ' selected="selected"';
			}
			$dropDown_html .= ' >- ' . JText::_('COM_RSGALLERY2_TOP_GALLERY') . ' -</option>';
		}

		$dropDown_html .= galleryUtils::addToGalSelectList(0, 0, $gallery_id, $galleriesAllowed);
		echo $dropDown_html . "</select>";
	}

	/**
	 * Show gallery select list according to the permissions of the logged in user
	 *
	 * permissions Create Own
	 *
	 * @param string  $select_name    Name of the select box, defaults to 'catid'
	 *                                Used 2016.05.24 'gallery_id', 'parent'
	 * @param integer $gallery_id     ID of selected gallery
	 * @param string  $js             Additional select tag attributes
	 * @param bool    $showTopGallery show Top Gallery to select, default no
	 *
		 * @since 4.3.0
 */
	static function showUserGalSelectListCreateAllowed($select_name = 'catid', $gallery_id = null, $js = '', $showTopGallery = false)
	{
		$user = JFactory::getUser();

		//Get gallery Id's where create is allowed and write to string
		$galleriesAllowed = rsgAuthorisation::authorisationCreate_galleryList();

		$dropDown_html = '<select name="' . $select_name . '" ' . $js . '><option value="-1" selected="selected" >' . JText::_('COM_RSGALLERY2_SELECT_GALLERY_FROM_LIST') . '</option>';

		if ($showTopGallery)
		{
			$dropDown_html .= "<option value=0";
			// Disable Top gallery when no create permission for component
			if (!$user->authorise('core.create', 'com_rsgallery2'))
			{
				$dropDown_html .= ' disabled="disabled"';
			}
			if ($gallery_id == 0)
			{
				$dropDown_html .= ' selected="selected"';
			}
			$dropDown_html .= ' >- ' . JText::_('COM_RSGALLERY2_TOP_GALLERY') . ' -</option>';
		}

		$dropDown_html .= galleryUtils::addToGalSelectList(0, 0, $gallery_id, $galleriesAllowed);
		echo $dropDown_html . "</select>";
	}

	/**
	 * Add galleries to the gallery select list according to the permissions of the logged in user
	 *
	 * @param integer $level            Level in gallery tree
	 * @param integer $galid            ID of current node in gallery tree
	 * @param integer $gallery_id       ID of selected gallery
	 * @param int []  $galleriesAllowed ID of selected gallery
	 *
	 * @return string HTML to add
		 * @since 4.3.0
 */
	static function addToGalSelectList($level, $galid, $gallery_id, $galleriesAllowed)
	{
		// provided by Klaas on Dec.13.2007
		$database      = JFactory::getDBO();
		$my       = JFactory::getUser();//giobag
		$my_id    = $my->id; //giobag

		$dropDown_html = "";

  // giobag 2017.07.21 Restrict added galleries to galleries owned by logged in user 
		// $query = 'SELECT * FROM `#__rsgallery2_galleries` WHERE `parent` = ' . (int) $galid . ' ORDER BY `ordering` ASC';
		$query = 'SELECT * FROM `#__rsgallery2_galleries` WHERE `uid` = ' . (int) $my_id . ' and `parent` = ' . (int) $galid . ' ORDER BY `ordering` ASC';//giobag
		
		
		$database->setQuery($query);

		$rows = $database->loadObjectList();
		foreach ($rows as $row)
		{
			$dropDown_html .= "<option value=\"$row->id\"";
			// Disable when action not allowed and disallowed parent is not current parent
			if (!in_array($row->id, $galleriesAllowed))
			{
				if ($row->id != $gallery_id)
				{
					$dropDown_html .= ' disabled="disabled"';
				}
			}
			if ($row->id == $gallery_id)
			{
				$dropDown_html .= ' selected="selected"';
			}

			$dropDown_html .= " >";
			$indent = "";
			for ($i = 0; $i < $level; $i++)
			{
				$indent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			}
			if ($level)
			{
				$indent .= "|--&nbsp;";
			}
			$dropDown_html .= $indent . $row->name . "</option>\n";
			$dropDown_html .= galleryUtils::addToGalSelectList($level + 1, $row->id, $gallery_id, $galleriesAllowed);
		}

		return $dropDown_html;
	}

	/** //MK// [todo] only for allowed parents...
	 * build the select list to choose a parent gallery for a specific user
	 *
	 * @param int     $galleryid current gallery id , defaults to null
	 * @param string  $listName  selectbox name, defaults to 'galleryid'
	 * @param boolean $style     Dropdown(false) or Liststyle(true), defaults to true
	 *
	 * @return string HTML representation for selectlist
	 * Seems to be unused in v3.1.0
		 * @since 4.3.0
 */
	static function createGalSelectList($galleryid = null, $listName = 'galleryid', $style = true)
	{
		$database = JFactory::getDBO();
		$my       =& JFactory::getUser();
		$my_id    = $my->id;
		if ($style == true)
		{
			$size = ' size="10"';
		}
		else
		{
			$size = ' size="1"';
		}
		// get a list of the menu items
		// excluding the current menu item and its child elements
		$query = 'SELECT *'
			. ' FROM `#__rsgallery2_galleries`'
			. ' WHERE `published` != -2'
			. ' AND `uid` = ' . (int) $my_id
			. ' ORDER BY `parent`, `ordering`';

		$database->setQuery($query);

		$mitems = $database->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();

		if ($mitems)
		{
			// first pass - collect children
			foreach ($mitems as $v)
			{
				$pt   = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		// assemble menu items to the array
		$mitems   = array();
		$mitems[] = JHtml::_("Select.option", '0', JText::_('COM_RSGALLERY2_TOP_GALLERY'));

		foreach ($list as $item)
		{
			$mitems[] = JHtml::_("Select.option", $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
		}

		$output = JHtml::_("select.genericlist", $mitems, $listName, 'class="inputbox"' . $size, 'value', 'text', $galleryid);

		echo $output;
	}

	/**
	 * build the select list to choose a gallery
	 * based on options/galleries.class.php:galleryParentSelectList()
	 *
	 * @param int     $galleryid  current gallery id , defaults to null
	 * @param string  $listName   selectbox name, defaults to 'galleryid'
	 * @param boolean $style      Dropdown(false) or Liststyle(true), defaults to true
	 * @param string  $javascript javascript entries ( e.g: 'onChange="form.submit();"' )
	 * @param int     $showUnauthorised
	 * @param bool    $excludeTopGallery
	 *
	 * @return string HTML representation for selectlist
		 * @since 4.3.0
 */
	static function galleriesSelectList($galleryid = null, $listName = 'gallery_id', $style = true,
		$javascript = null, $showUnauthorised = 1, $excludeTopGallery = false)
	{
		$database = JFactory::getDBO();
		if ($style == true)
		{
			$size = ' size="10"';
		}
		else
		{
			$size = ' size="1"';
		}
		// get a list of the menu items
		// excluding the current menu item and its child elements
		//$query = "SELECT *"; //MK [change] [J1.6 needs parent_id and title instead of parent and name]
		$query = "SELECT *, `parent` AS `parent_id`, `name` AS `title` "
			. " FROM `#__rsgallery2_galleries`"
			. " WHERE `published` != -2"            //MK// [change] [What is -2 for: not J1.0 nor J1.5...]	
			. " ORDER BY `parent`, `ordering`";

		$database->setQuery($query);

		$mitems = $database->loadObjectList();

		// establish the hierarchy of the menu
		$children = array();

		if ($mitems)
		{
			// first pass - collect children
			foreach ($mitems as $v)
			{
				$pt   = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		// assemble menu items to the array
		$mitems   = array();
		$mitems[] = JHtml::_("Select.option", '-1', JText::_('COM_RSGALLERY2_SELECT_GALLERY'));
		// Show top gallery item
		if ($excludeTopGallery == false)
		{
			$mitems[] = JHtml::_("Select.option", '0', '- ' . JText::_('COM_RSGALLERY2_TOP_GALLERY') . ' -');
		}
		foreach ($list as $item)
		{
			$canCreateInGallery = JFactory::getUser()->authorise('core.create', 'com_rsgallery2.gallery.' . $item->id);
			//MK: The original treename holds &#160; as a non breacking space for subgalleries, but JHtmlSelect::option cannot handle that, nor &nbsp;, so replaced string
			$item->treename     = str_replace('&#160;&#160;', '...', $item->treename);
			//When $showUnauthorised is false only galleries where create is allowed or which are the current selected gallery can be choosen.
			if ($canCreateInGallery OR $showUnauthorised OR $galleryid == $item->id)
			{
				$mitems[] = JHtml::_("Select.option", $item->id, '' . $item->treename);
			}
			else
			{
				//May not be selected: give 0 value instead of $item->id
				$mitems[] = JHtml::_("Select.option", 0, '' . $item->treename . ' - ' . JText::_('JDISABLED'), 'value', 'text', true);
			}
		}

		$output = JHtml::_("select.genericlist", $mitems, $listName, 'class="inputbox"' . $size . ' ' . $javascript, 'value', 'text', $galleryid, false);

		return $output;
	}

	/**
	 * Retrieves the thumbnail image. presented in the category overview
	 *
	 * @param int    $catid  Category id
	 * @param int    $height image height
	 * @param int    $width  image width
	 * @param string $class  Class name to format thumb view in css files
	 *
	 * @return string html tag, showing the thumbnail
	 * @todo being depreciated in favor of $rsgGallery->thumb() and $rsgDisplay functions
	 	 * @since 4.3.0
*/
	static function getThumb($catid, $height = 0, $width = 0, $class = "")
	{
		$thumb_id = null;

		$database = JFactory::getDBO();

		//Setting attributes for image tag
		$imgatt = "";
		if ($height > 0)
		{
			$imgatt .= " height=\"$height\" ";
		}
		if ($width > 0)
		{
			$imgatt .= " width=\"$width\" ";
		}
		if ($class != "")
		{
			$imgatt .= " class=\"$class\" ";
		}
		else
		{
			$imgatt .= " class=\"rsg2-galleryList-thumb\" ";
		}
		//If no thumb, show default image.
		if (galleryUtils::getFileCount($catid) == 0)
		{
			$thumb_html = "<img $imgatt src=\"" . JURI_SITE . "/components/com_rsgallery2/images/no_pics.gif\" alt=\"No pictures in gallery\" />";
		}
		else
		{
			//Select thumb setting for specific gallery("Random" or "Specific thumb")
			//$sql = 'SELECT `thumb_id` FROM `#__rsgallery2_galleries` WHERE `id` = '. (int) $catid;
			$query = $database->getQuery(true);
			$query->select('thumb_id')
				->from($database->quoteName('#__rsgallery2_galleries'))
				->where($database->quoteName('id') . ' = ' . (int) $catid);
			$database->setQuery($query);
			$thumb_id = $database->loadResult();

			$list = galleryUtils::getChildList((int) $catid);
			if ($thumb_id == 0)
			{
				//Random thumbnail
				// $sql = "SELECT `name` FROM `#__rsgallery2_files` WHERE `gallery_id` IN ($list) AND `published` = 1 ORDER BY rand() LIMIT 1";
				$query = $database->getQuery(true);
				$query->select('name')
					->from($database->quoteName('#__rsgallery2_files'))
					->where($database->quoteName('gallery_id') . ' IN (' . $list . ') AND '
						. $database->quoteName('published') . ' = ' . (int) 1)
					->order('rand()')
					->limit('1');
				$database->setQuery($query);
				$thumb_name = $database->loadResult();
			}
			else
			{
				//Specific thumbnail
				$thumb_name = galleryUtils::getFileNameFromId($thumb_id);
			}
			//$thumb_html = "<img $imgatt src=\"".imgUtils::getImgThumbPath($thumb_name)."\" alt=\"\" />";
			$thumb_html = "<img $imgatt src=\"" . imgUtils::getImgThumb($thumb_name) . "\" alt=\"\" />";
		}

		return $thumb_html;
	}

	/**
	 * Returns number of published items within a specific gallery and perhaps its children
	 *
	 * @param int  $id       Gallery id
	 * @param bool $withKids Get the number if items in the child-galleries or not
	 *
	 * @return int Number of items in gallery and possibly subgalleries
		 * @since 4.3.0
 */
	static function getFileCount($id, $withKids = true)
	{
		$database = JFactory::getDBO();
		if ($withKids)
		{
			$list = galleryUtils::getChildList((int) $id);
		}
		else
		{
			$list = (int) $id;
		}
		$query = 'SELECT COUNT(1) FROM `#__rsgallery2_files` WHERE ((`gallery_id` IN (' . $list . ')) AND (`published` = 1))';
		$database->setQuery($query);
		$count = $database->loadResult();

		return $count;
	}

	/**
	 * Retrieves category name, based on the category id
	 *
	 * @param int $id The ID of the currently selected category
	 *
	 * @return string Category Name
		 * @since 4.3.0
 */
	static function getCatnameFromId($id)
	{
		$database = JFactory::getDBO();
		$query    = 'SELECT `name` FROM `#__rsgallery2_galleries` WHERE `id` = ' . (int) $id;
		$database->setQuery($query);
		$catname = $database->loadResult();

		return $catname;
	}

	/**
	 * Retrieves category ID, based on the filename id
	 *
	 * @param int $id The ID of the currently selected file
	 *
	 * @return string Category ID
		 * @since 4.3.0
 */
	static function getCatIdFromFileId($id)
	{
		$database = JFactory::getDBO();
		$query    = 'SELECT `gallery_id` FROM `#__rsgallery2_files` WHERE `id` = ' . (int) $id;
		$database->setQuery($query);
		$gallery_id = $database->loadResult();

		return $gallery_id;
	}

	/**
	 * Retrieves filename, based on the filename id
	 *
	 * @param int $id The ID of the currently selected file
	 *
	 * @return string Filename
		 * @since 4.3.0
 */
	static function getFileNameFromId($id)
	{
		$database = JFactory::getDBO();
		$query    = 'SELECT `name` FROM `#__rsgallery2_files` WHERE `id` = ' . (int) $id;
		$database->setQuery($query);
		$filename = $database->loadResult();

		return $filename;
	}

	/**
	 * Retrieves title, based on the filename id
	 *
	 * @param int $id The ID of the currently selected file
	 *
	 * @return string title
		 * @since 4.3.0
 */
	static function getTitleFromId($id)
	{
		$database = JFactory::getDBO();
		$query    = 'SELECT `title` FROM `#__rsgallery2_files` WHERE `id` = ' . (int) $id;
		$database->setQuery($query);
		$title = $database->loadResult();

		return $title;
	}

	/**
	 * Returns parent ID from chosen gallery
	 *
	 * @param int $gallery_id Gallery ID
	 *
	 * @return int Parent ID
		 * @since 4.3.0
 */
	static function getParentId($gallery_id)
	{
		$database = JFactory::getDBO();
		$sql      = 'SELECT `parent` FROM `#__rsgallery2_galleries` WHERE `id` = ' . (int) $gallery_id;
		$database->setQuery($sql);
		$parent = $database->loadResult();

		return $parent;
	}

	/**
	 * Creates new thumbnails with new settings
	 *
	 * @param int $catid Category ID
	 *
		 * @since 4.3.0
 */
	static function regenerateThumbs($catid = null)
	{
		global $rsgConfig;
		$i = 0;

		// $files  = mosReadDirectory( JPATH_ROOT.$rsgConfig->get('imgPath_original') );
		// mosReadDirectory deprecated	As of version 1.5
		// mosReadDirectory ( $path, $filter='.', $recurse=false, $fullpath=false  )
		// use {@link JFolder::files()} or {@link JFolder::folders()} instead
		// files($path, $filter = '.', $recurse = false, $full = false
		$files = JFolder::files(JPATH_ROOT . $rsgConfig->get('imgPath_original'));
		//check if size is changed
		foreach ($files as $file)
		{
			if (imgUtils::makeThumbImage(JPATH_ROOT . $rsgConfig->get('imgPath_original') . $file))
			{
				continue;
			}
			else
			{
				$error[] = $file;
			}
			$i++;
		}
	}

	/**
	 * @param $xid
	 * ToDo: Fix: Remove
     *
	 * @depreciated use rsgGallery->hasNewImages() instead;
		 * @since 4.3.0
 */
	static function newImages($xid)
	{
		$database = JFactory::getDBO();
		$lastweek = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
		$lastweek = date("Y-m-d H:m:s", $lastweek);

		$query = 'SELECT * FROM `#__rsgallery2_files` WHERE `date` >= ' . $database->quote($lastweek) . ' AND `published` = 1 AND `gallery_id` = ' . (int) $xid;
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$gallery_id = $row->gallery_id;
				if ($gallery_id == $xid)
				{
					echo JText::_('COM_RSGALLERY2_NEW-');
					break;
				}
			}
		}
		else
		{
			echo "";
		}
	}

	/**
	 * This function will retrieve the user Id's of the owner of this gallery.
	 *
	 * @param integer $catid id of category
	 *
	 * @return int the requested user id
	 * Seems to be no longer used in 3.1.0
	 	 * @since 4.3.0
*/
	static function getUID($catid)
	{
		$database = JFactory::getDBO();
		$query    = 'SELECT `uid` FROM `#__rsgallery2_galleries` WHERE `id` = ' . (int) $catid;
		$database->setQuery($query);
		$uid = $database->loadResult();

		return $uid;
	}

	/**
	 * This function returns the number of created galleries by the logged in user
	 *
	 * @param int $id user ID
	 *
	 * @return int number of created categories
	 	 * @since 4.3.0
*/
	static function userCategoryTotal($id)
	{
		$database = JFactory::getDBO();
		$query    = 'SELECT COUNT(1) FROM `#__rsgallery2_galleries` WHERE `uid` = ' . (int) $id;
		$database->setQuery($query);
		$cats = $database->loadResult();

		return $cats;
	}

	/**
	 * This function returns the number of uploaded images  by the logged in user
	 *
	 * @param int $id user ID
	 *
	 * @return int $id number of uploaded images
		 * @since 4.3.0
 */
	static function userImageTotal($id)
	{
		$database = JFactory::getDBO();
		$query    = 'SELECT COUNT(1) FROM `#__rsgallery2_files` WHERE `userid` = ' . (int) $id;
		$database->setQuery($query);
		$result = $database->loadResult();

		return $result;
	}

	/**
	 * This function returns the number of uploaded images  by the logged in user
	 *
	 * @return gallery names in HTML
		 * @since 4.3.0
     */
	static function latestCats()
	{
		$my       = JFactory::getUser();
		$database = JFactory::getDBO();

		$query = "SELECT * FROM `#__rsgallery2_galleries` ORDER BY `id` DESC LIMIT 0,5";
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				?>
				<tr>
					<td><?php echo $row->name; ?></td>
					<td><?php echo galleryUtils::genericGetUsername($row->uid); ?></td>
					<td><?php echo $row->id; ?></td>
				</tr>
				<?php
			}
		}
		else
		{
			echo "<tr><td colspan=\"3\">" . JText::_('COM_RSGALLERY2_NO_NEW_ENTRIES') . "</td></tr>";
		}
	}

	/**
	 * This function will retrieve the user name based on the user id
	 *
	 * @param int $uid user id
	 *
	 * @return string the username
	 * @todo isn't there a joomla function for this?
	 	 * @since 4.3.0
*/
	static function genericGetUsername($uid)
	{
		$my       = JFactory::getUser();
		$database = JFactory::getDBO();
		global $name;

		$query = 'SELECT `username` FROM `#__users` WHERE `id` = ' . (int) $uid;
		$database->setQuery($query);
		$name = $database->loadResult();

		return $name;
	}

	/**
	 * This function will show the 5 last uploaded images
		 * @since 4.3.0
 */
	static function latestImages()
	{
		global $rows;
		$my       = JFactory::getUser();
		$database = JFactory::getDBO();

		$lastweek = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
		$lastweek = date("Y-m-d H:m:s", $lastweek);
		$query    = 'SELECT * FROM `#__rsgallery2_files` WHERE (`date` >= ' . $database->quote($lastweek) . ' AND `published` = 1) ORDER BY `id` DESC LIMIT 0,5';

		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				?>
				<tr>
					<td><?php echo $row->name; ?></td>
					<td><?php echo galleryUtils::getCatnameFromId($row->gallery_id); ?></td>
					<td><?php echo $row->date; ?></td>
					<td><?php echo galleryUtils::genericGetUsername($row->userid); ?></td>
				</tr>
				<?php
			}
		}
		else
		{
			echo "<tr><td colspan=\"4\">" . JText::_('COM_RSGALLERY2_NO_NEW_ENTRIES') . "</td></tr>";
		}
	}

	/**
	 * replaces spaces with underscores
	 * replaces other weird characters with dashes
	 *
	 * @param string $text input text
	 *
	 * @return string cleaned up text
		 * @since 4.3.0
 **/
	static function replaceStrangeChar($text)
	{
		$text = str_replace(" ", "_", $text);
		$text = preg_replace('/[^a-z0-9_\-\.]/i', '_', $text);

		return $text;
	}

	/**
	 * Retrieves file ID based on the filename
	 *
	 * @param string $filename filename
	 *
	 * @return integer File ID
		 * @since 4.3.0
 */
	static function getFileIdFromName($filename)
	{
		$database = JFactory::getDBO();
		$sql      = 'SELECT `id` FROM `#__rsgallery2_files` WHERE `name` = ' . $database->quote($filename);
		$database->setQuery($sql);
		$id = $database->loadResult();

		return $id;
	}

	/**
	 * @param string $tbl
	 * @param string $where , defaults to null
	 *
	 * @return bool
		 * @since 4.3.0
 */
	static function reorderRSGallery($tbl, $where = null)
	{
		// reorders either the categories or images within a category
		// it is necessary to call this whenever a shuffle or deletion is performed
		$database = JFactory::getDBO();

		$query = 'SELECT `id`, `ordering` FROM ' . $tbl
			. ($where ? ' WHERE ' . $where : '')
			. ' ORDER BY `ordering` ';
		$database->setQuery($query);
		if (!($rows = $database->loadObjectList()))
		{
			return false;
		}

		// first pass, compact the ordering numbers
		$n = count($rows);

		for ($i = 0; $i < $n; $i++)
		{
			$rows[$i]->ordering = $i + 1;
			$query              = 'UPDATE ' . $tbl . ' '
				. ' SET `ordering`=' . (int) $rows[$i]->ordering
				. ' WHERE `id` =' . (int) $rows[$i]->id;
			$database->setQuery($query);
			$database->execute();
		}

		return true;
	}

	/**
	 * Functions shows a warning box above the control panel is something is preventing
	 * RSGallery2 from functioning properly
		 * @since 4.3.0
 */
	static function writeWarningBox()
	{
		global $rsgConfig;
		require_once(JPATH_COMPONENT_ADMINISTRATOR . '/includes/img.utils.php');
		
		//Detect image libraries
		$html  = '';
		$count = 0;
		if ((!GD2::detect()) and (!GD::detect()) and (!imageMagick::detect()) and (!Netpbm::detect()))
		{
			$html .= "<p style=\"color: #CC0000;font-size:smaller;\"><img src=\"" . JURI_SITE . "/includes/js/ThemeOffice/warning.png\" alt=\"\">&nbsp;" . JText::_('COM_RSGALLERY2_NO_IMGLIBRARY') . "</p>";
		}

		//Check availability and writable of folders
		$folders = array(
			$rsgConfig->get('imgPath_display'),
			$rsgConfig->get('imgPath_thumb'),
			$rsgConfig->get('imgPath_original'),
			'/images/rsgallery',
			'/media'
		);
		foreach ($folders as $folder)
		{
			if (file_exists(JPATH_ROOT . $folder) && is_dir(JPATH_ROOT . $folder))
			{
				$perms = substr(sprintf('%o', fileperms(JPATH_ROOT . $folder)), -4);
				if (!is_writable(JPATH_ROOT . $folder))
				{
					$html .= "<p style=\"color: #CC0000;font-size:smaller;\"><img src=\"" . JURI_SITE . "/includes/js/ThemeOffice/warning.png\" alt=\"\">&nbsp;<strong>" . JPATH_ROOT . $folder . "</strong>" . JText::_('COM_RSGALLERY2_IS_NOT_WRITABLE') . "($perms)";
				}
				// Check if the folder has a file index.html, if not, create it, but not for media folder
				if ((!JFile::exists(JPATH_ROOT . $folder . '/index.html')) AND ($folder != "/media"))
				{
					$buffer = '';    //needed: Cannot pass parameter 2 [of JFile::write()] by reference...
					JFile::write(JPATH_ROOT . $folder . '/index.html', $buffer);
				}
			}
			else
			{
				$html .= "<p style=\"color: #CC0000;font-size:smaller;\"><img src=\"" . JURI_SITE . "/includes/js/ThemeOffice/warning.png\" alt=\"\">&nbsp;<strong>" . JPATH_ROOT . $folder . "</strong>" . JText::_('COM_RSGALLERY2_FOLDER_NOTEXIST');
			}
		}
		if ($html !== '')
		{
			?>
			<div style="clear: both; margin: 3px; margin-top: 10px; padding: 5px 15px; display: block; float: left; border: 1px solid #cc0000; background: #ffffcc; text-align: left; width: 50%;">
				<p style="color: #CC0000;"><?php echo JText::_('COM_RSGALLERY2_THE_FOLLOWING_SETTINGS_PREVENT_RSGALLERY2_FROM_WORKING_WITHOUT_ERRORS') ?></p>
				<?php echo $html; ?>
				<p style="color: #CC0000;text-align:right;">
					<a href="index.php?option=com_rsgallery2"><?php echo JText::_('COM_RSGALLERY2_REFRESH') ?></a></p>
			</div>
			<div class='rsg2-clr'>&nbsp;</div>
			<?php
		}
	}

	/**
	 * Write downloadlink for image
	 *
	 * @param int    $id       image ID
	 * @param bool   $showtext Button or HTML link (button/link)
	 * @param string $type
	 *                         writes HTML for downloadlink
	 *
		 * @since 4.3.0
 */
	static function writeDownloadLink($id, $showtext = true, $type = 'button')
	{
		echo "<div class=\"rsg2-toolbar\">";
		if ($type == 'button')
		{
			?>
			<a href="<?php echo JRoute::_('index.php?option=com_rsgallery2&task=downloadfile&id=' . $id); ?>">
				<img height="20" width="20" src="<?php echo JURI_SITE; ?>/administrator/images/download_f2.png" 
                     alt="<?php echo JText::_('COM_RSGALLERY2_DOWNLOAD') ?>">
				<?php
				if ($showtext == true)
				{
					?>
					<br /><span style="font-size:smaller;"><?php echo JText::_('COM_RSGALLERY2_DOWNLOAD') ?></span>
					<?php
				}
				?>
			</a>
			<?php
		}
		else
		{
			?>
			<a href="<?php echo JRoute::_('index.php?option=com_rsgallery2&task=downloadfile&id=' . $id); ?>"><?php echo JText::_('COM_RSGALLERY2_DOWNLOAD') ?></a>
			<?php
		}
		echo "</div><div class=\"rsg2-clr\">&nbsp;</div>";
	}

	/**
	 * @param $gallery
	 *
	 * @return string|""
		 * @since 4.3.0
 */
	static function writeGalleryStatus($gallery)
	{
		global $rsgConfig;
		$my = JFactory::getUser();

		// return if status is not displayed
		if (!$rsgConfig->get('displayStatus'))
		{
			return "";
		}

		$owner  = JHtml::tooltip(JText::_('COM_RSGALLERY2_YOU_ARE_THE_OWNER_OF_THIS_GALLERY'),
			null,
			'../../../components/com_rsgallery2/images/status_owner.png', null, null, 0);
		$upload = JHtml::tooltip(JText::_('COM_RSGALLERY2_YOU_CAN_UPLOAD_IN_THIS_GALLERY'),
			null,
			'../../../components/com_rsgallery2/images/status_upload.png', null, null, 0);

		$unpublished = JHtml::tooltip(JText::_('COM_RSGALLERY2_THIS_GALLERY_IS_NOT_PUBLISHED'),
			null,
			'../../../components/com_rsgallery2/images/status_hidden.png', null, null, 0);

		$html = "";

		$uid       = $gallery->uid;
		$mid       = $my->id;
		$published = $gallery->published;

		// Check if user is owner of the gallery (user 0 is not logged in and does just view galleries)
		if ($gallery->uid == $mid)
		{
			$html .= $owner;
		}

		//Check if gallery is published
		if ($gallery->published == 0)
		{
			$html .= $unpublished;
		}

		if (rsgAuthorisation::authorisationCreate($gallery->id))
		{
			$html .= $upload;
		}

		return $html;
	}

	/**
	 * Get a list of published (gran)child galleries
	 *
	 * @param int $gallery_id Gallery id for which the child galleries must be found
	 *
	 * @return string String with all child galleries separated by a comma (e.g. 1,2,3)
		 * @since 4.3.0
 */
	static function getChildList($gallery_id)
	{
		$array = galleryUtils::getChildListArray($gallery_id);
		$list  = implode(",", array_unique($array));

		return $list;
	}

	/**
	 * Get a list of published (gran)child galleries
	 *
	 * @param int    $gallery_id Gallery id for which the child galleries must be found
	 * @param int [] $array      , defaults to null
	 *
	 * @return int [] Array with all child galleries separated by a comma
		 * @since 4.3.0
 */
	static function getChildListArray($gallery_id, $array = null)
	{
		$database = JFactory::getDBO();

		$array[] = $gallery_id;

		$query = $database->getQuery(true);
		$query->select('id');
		$query->from('#__rsgallery2_galleries');
		$query->where('parent = ' . (int) $gallery_id);
		$query->where('published =  1');
		$database->setQuery($query);
		$database->execute();
		$result = $database->loadColumn();

		//If there are children in the array, merge them with the ones we know off ($array)
		if (count($result) > 0 && is_array($result))
		{
			$array = array_merge($array, $result);
		}
		foreach ($result as $value)
		{
			$array = array_merge(galleryUtils::getChildListArray($value, $array), $array);
		}

		return array_unique($array);
	}

	/**
	 * @return string
		 * @since 4.3.0
 */
	static function showFontList()
	{
		global $rsgConfig;

		$fontlist = array();

		$selected = $rsgConfig->get('watermark_font');
		$fonts    = JFolder::files(JPATH_RSGALLERY2_ADMIN . '/fonts', 'ttf');
		foreach ($fonts as $font)
		{
			$fontlist[] = JHtml::_("Select.option", $font);
		}
		$list = JHtml::_("select.genericlist", $fontlist, 'watermark_font', '', 'value', 'text', $selected);

		return $list;
	}

	/**
	 * Writes selected amount of characters. If there are more, the tail will be printed,
	 * identifying there is more
	 *
	 * @param string $text   Full text
	 * @param int    $length Number of characters to display
	 * @param string $tail   Tail to print after substring is printed
	 *
	 * @return string Subtext, followed by tail
		 * @since 4.3.0
 */
	static function subText($text, $length = 20, $tail = "...")
	{
		$text = trim($text);
		$txtl = strlen($text);
		jimport('joomla.filter.output');

		$tail = JHtml::tooltip(JFilterOutput::ampReplace($text), null, null, $tail, null, 0);
		if ($txtl > $length)
		{
			for ($i = 1; $text[$length - $i] != " "; $i++)
			{
				if ($i == $length)
				{
					return substr($text, 0, $length) . $tail;
				}
			}
			$text = substr($text, 0, $length - $i + 1) . $tail;
		}

		return $text;
	}

	/**
	 * Checks if a specific component is installed
	 *
	 * @param string $component_name Component name
	 *
	 * @return int
		 * @since 4.3.0
 */
	static function isComponentInstalled($component_name)
	{
		$database = JFactory::getDBO();
		// $sql = 'SELECT COUNT(1) FROM `#__extensions` WHERE `element` = '. $database->quote($component_name);
		$query = $database->getQuery(true);
		$query->select('name')
			->from('#__extensions')
			->where('element=' . $database->quote($component_name))
			->limit('1');
		$database->setQuery($query);

		$result = $database->loadResult();
		if ($result > 0)
		{
			$notice = 1;
		}
		else
		{
			$notice = 0;
		}

		return $notice;
	}

	/**
	 * Higlights text based on keywords
	 *
	 * @param string $string   Text to search in.
	 * @param string $keywords Keywords to search for
	 * @param string $color    , defaults to yellow ? not used ?
	 *
	 * @return string
		 * @since 4.3.0
 */
	static function highlight_keywords($string, $keywords, $color = "yellow")
	{
		if ($keywords != "" || $keywords != null)
		{
			$words = explode(" ", $keywords);
			foreach ($words as $word)
			{
				$position = 0;
				while ($position !== false)
				{
					$position = strpos(strtolower($string), strtolower($word), $position);
					if ($position !== false)
					{
						$replace_string = substr($string, $position, strlen($word));
						if ($position == 0)
						{
							if (!ctype_alnum($string[strlen($word)]))
							{
								$replace_string = "<span style=\"background-color: yellow;\">" . $replace_string . "</span>";
								$string         = substr_replace($string, $replace_string, $position, strlen($word));
							}
						}
						elseif (!ctype_alnum($string[$position - 1]) && strlen($string) == $position + strlen($word))
						{
							$replace_string = "<span style=\"background-color: yellow;\">" . $replace_string . "</span>";
							$string         = substr_replace($string, $replace_string, $position, strlen($word));
						}
						elseif (!ctype_alnum($string[$position - 1]) && !ctype_alnum($string[$position + strlen($word)]))
						{
							$replace_string = "<span style=\"background-color: yellow;\">" . $replace_string . "</span>";
							$string         = substr_replace($string, $replace_string, $position, strlen($word));
						}
						$position = $position + strlen($replace_string);
					}
				}
			}
		}

		return $string;
	}

	/**
	 * Method to return a list of all galleries that a user has permission for a given action
	 *
	 * @param    string $action The action
	 *
	 * @return    int []    List of galleries that the user can do this action to (empty array if none). Galleries may be unpublished
		 * @since 4.3.0
 */
	static function getAuthorisedGalleries($action)
	{
		$user = JFactory::getUser();
		// Get all gallery rows for the component and check each one
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id')
			->from('#__rsgallery2_galleries');
		//		->where('published = 1');
		$db->setQuery($query);
		$allGalleries     = $db->loadObjectList('id');
		$allowedGalleries = array();
		foreach ($allGalleries as $gallery)
		{
			$asset   = 'com_rsgallery2.gallery.' . $gallery->id;
			$allowed = $user->authorise($action, $asset);
			if ($allowed)
			{
				$allowedGalleries[] = (int) $gallery->id;
			}
		}

		return $allowedGalleries;
	}

}//end class galleryUtils
?>
