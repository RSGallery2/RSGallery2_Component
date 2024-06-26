﻿<?php
/**
 * ImageReferences collect all information about image artefacts
 *
 * @package       Rsgallery2
 * @copyright (C) 2016-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author        finnern
 *                RSGallery2 is Free Software
 */
// no direct access
defined('_JEXEC') or die;

// Include the JLog class.
jimport('joomla.log.log');

// access to the content of the install.mysql.utf8.sql file
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/classes/ImageReference.php');

/**
 * ImageReferences collect all information about image artefacts
 * The information contains appearance and properties of images
 * where at least one part is missing. Artefacts appear when a image
 * file or a database entry is missing
 * Examples: display image file is existing but no database entry is matching
 * This is used in maintConsolidateDb to fix these artefacts
 *
 * @since 4.3.0
 */
class ImageReferences
{
	/**
     * List of image references
	 * @var ImageReference []
	 */
	protected $ImageReferenceList;

	/**
	 * @var bool
	 */
	protected $IsAnyImageMissingInDB;
    /**
     * @var bool
     */
	protected $IsAnyImageMissingInDisplay;
    /**
     * @var bool
     */
	protected $IsAnyImageMissingInOriginal;
    /**
     * @var bool
     */
	protected $IsAnyImageMissingInThumb;
    /**
     * @var bool
     */
	protected $IsAnyImageMissingInWatermarked;
    /**
     * @var bool
     */
	protected $IsAnyOneImageMissing;
	/**
	 * @var bool
	 */
	public $UseWatermarked;

	/**
	 * ImageReference constructor. init all variables
	 * @param bool $watermarked
	 *
	 * @since 4.3.0
	 */
	public function __construct($watermarked = false)
	{
		$this->ImageReferences = array();

		$this->IsAnyImageMissingInDB          = false;
		$this->IsAnyImageMissingInDisplay     = false;
		$this->IsAnyImageMissingInOriginal    = false;
		$this->IsAnyImageMissingInThumb       = false;
		$this->IsAnyImageMissingInWatermarked = false;
		$this->IsAnyOneImageMissing           = false;

		if ($watermarked)
		{
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/includes/ImgWatermarkNames.php';
		}

		$this->UseWatermarked = $watermarked;
	}

    /**
     *
     * @return ImageReference[]
     *
     * @since version 4.3
     */
	public function getImageReferenceList()
	{

		// ??? if empty -> CollectImageReferences ...

		return $this->ImageReferenceList;
	}

	// ToDO: Do i need this function ?
    /**
     * property accessor
     * shall only be used for IsAny...
     *
     * @param string $property
     *
     * @return mixed (mostly bool)
     *
     * @since version 4.3
     */
	public function __get($property)
	{
		if (property_exists($this, $property))
		{
			return $this->$property;
		}
	}

    /**
     * Collects all data of all images and then creates the list
     * of image artefacts
     *
     * @return string Message of creating the data if any
     *
     * @since version 4.3
     */
	public function CollectImageReferences()
	{
		global $rsgConfig;

		$msg = '';

		//--- Collect data of all images --------------------------------------------------

		$DbImageGalleryList = $this->getDbImageGalleryList();  // Is tunneled to create it only once
		//$DbImageGalleryList = array_map('strtolower', $DbImageGalleryList);
		//$DbImageGalleryList = array_change_key_case($DbImageGalleryList, CASE_LOWER);
		$DbImageNames = $this->getDbImageNames();
		$DbImageNames = array_map('strtolower', $DbImageNames);

		$files_display  = $this->getFilenameArray($rsgConfig->get('imgPath_display'));
		$files_original = $this->getFilenameArray($rsgConfig->get('imgPath_original'));
		$files_thumb    = $this->getFilenameArray($rsgConfig->get('imgPath_thumb'));

		// Watermarked: Start with empty array
		$files_watermarked = array();
		if ($this->UseWatermarked)
		{
			$files_watermarked = $this->getFilenameArray($rsgConfig->get('imgPath_watermarked'));
		}

		//$files_merged = array_unique(array_merge($DbImageNames, $files_display,
		//	$files_original, $files_thumb, $files_watermarked));

		$files_merged = array_unique(array_merge($DbImageNames, $files_display,
			$files_original, $files_thumb));

		//--- Check 4 missing data. Collect result in ImageReferenceList ---------------------

		$msg .= $this->CreateImagesData($files_merged, $DbImageNames, $DbImageGalleryList, $files_display,
			$files_original, $files_thumb, $files_watermarked);

		return $msg;
	}

    /**
     * Collects existing image name list with gallery_id from database
     *
     * @return array of object (name and gallery_id)
     *
     * @since version 4.3
     */
	private function getDbImageGalleryList()
	{
		/*
		$database = JFactory::getDBO();
		//Load all image names from DB in array
		$sql = "SELECT name FROM #__rsgallery2_files";
		$database->setQuery($sql);
		$names_db = rsg2_consolidate::arrayToLower($database->loadColumn());
		*/
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(array('name', 'gallery_id')))
			->from($db->quoteName('#__rsgallery2_files'))
			->order('name');

		$db->setQuery($query);
		//$rows =  $db->loadAssocList();
		$rows = $db->loadRowList();

		//--- Create assoc List ------------------------------
		$DbImageGalleryList = array();

		foreach ($rows as $row)
		{
			$DbImageGalleryList [strtolower($row[0])] = $row[1];
		}

		return $DbImageGalleryList;
	}

    /**
     * Collects existing image name list from database
     *
     * @return string [] image file names
     *
     * @since version 4.3
     */
	private function getDbImageNames()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('name'))
			->from($db->quoteName('#__rsgallery2_files'));

		$db->setQuery($query);
		$DbImageNames = $db->loadColumn();

		return $DbImageNames;
	}

	/**
	 * Fills an array with the file names, found in the specified directory
	 *
	 * @param string $dir Directory from Joomla root
	 *
	 * @return string [] file name array
     *
     * @since version 4.3
     */
	static function getFilenameArray($dir)
	{
		global $rsgConfig;

		//Load all image names from filesystem in array
		$dh = opendir(JPATH_ROOT . $dir);

		//Files to exclude from the check
		$exclude  = array('.', '..', 'Thumbs.db', 'thumbs.db');
		$allowed  = array('jpg', 'gif', 'png', 'jpeg');
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
	 *
	 * @since version 4.3
	 */
	static function arrayToLower($array)
	{
		$array = explode("|", strtolower(implode("|", $array)));

		return $array;
	}

	/**
     * Checks all occurrences of a image (information) and does collect uncomplete set as artifacts
	 * An artifact occurs when an expected image or DB reference is missing
	 *
	 * @param string [] $AllFiles     file names
	 * @param string [] $DbImageNames in lower case
	 * @param           $DbImageGalleryList
	 * @param           $files_display
	 * @param           $files_original
	 * @param           $files_thumb
	 * @param $files_watermarked
	 *
	 * @return string Message
	 *
	 * @throws Exception
	 *
	 * @since version 4.3
	 */
	private function CreateImagesData($AllFiles, $DbImageNames, $DbImageGalleryList,
		$files_display, $files_original, $files_thumb, $files_watermarked)
	{
		global $rsgConfig;

		$this->ImageReferenceList = array();

		$ValidWatermarkNames = array();

		// Not watermarked
		foreach ($AllFiles as $BaseFile)
		{
			//$MissingImage = false;

			$ImagesData                 = new ImageReference($this->UseWatermarked);
			$ImagesData->imageName      = $BaseFile;
			// $ImagesData->UseWatermarked = $this->UseWatermarked;

			$ImagesData->IsGalleryAssigned = false;
			if (in_array($BaseFile, $DbImageNames))
			{
				$ImagesData->IsImageInDatabase = true;

				// Check for missing gallery assignment. Use list read once -> ID-gallery id
				$GalleryId = $DbImageGalleryList [$BaseFile];
				if (!empty($GalleryId))
				{
					if ($GalleryId > 0)
					{
						$ImagesData->IsGalleryAssigned = true;
						$ImagesData->ParentGalleryId   = $GalleryId;
					}
				}
			}
			if (in_array($BaseFile, $files_display))
			{
				$ImagesData->IsDisplayImageFound = true;
			}

			if (in_array($BaseFile, $files_original))
			{
				$ImagesData->IsOriginalImageFound = true;
			}

			if (in_array($BaseFile, $files_thumb))
			{
				$ImagesData->IsThumbImageFound = true;
			}

			if ($this->UseWatermarked)
			{
				// Needs creation of hidden filename -> either from original or display folder
				$imageOrigin = 'original';
				$BaseFileWatermarked = ImgWatermarkNames::createWatermarkedFileName($BaseFile, $imageOrigin);
				if (in_array($BaseFileWatermarked, $files_watermarked))
				{
					$ImagesData->IsWatermarkedImageFound = true;
					$ImagesData->WatermarkedFileName = $BaseFileWatermarked;
					$ValidWatermarkNames [] = $BaseFileWatermarked;
				}
				else
				{
					$imageOrigin         = 'display';
					$BaseFileWatermarked = ImgWatermarkNames::createWatermarkedFileName($BaseFile, $imageOrigin);
					if (in_array($BaseFileWatermarked, $files_watermarked))
					{
						$ImagesData->IsWatermarkedImageFound = true;
						$ImagesData->WatermarkedFileName = $BaseFileWatermarked;
						$ValidWatermarkNames [] = $BaseFileWatermarked;
					}
				}
			}

			//-------------------------------------------------
			// Does file need to be handled ?
			//-------------------------------------------------
			// "do not care" used as watermarked images are not missing as such.
			// watermarked images will be created when displaying image
			if ($ImagesData->IsMainImageMissing(ImageReference::dontCareForWatermarked)
				|| !$ImagesData->IsImageInDatabase
				|| !$ImagesData->IsGalleryAssigned
			)
			{
				//--- parent gallery name ----------------------------------------------------

				if ($ImagesData->IsGalleryAssigned == true)
				{
					$ImagesData->ParentGallery = $this->getParentGalleryName($ImagesData->ParentGalleryId);
				}
				else
				{
					// Not existing
					// $ImagesData->ParentGalleryId = -1; // '0';
				}

				//--- ImagePath ----------------------------------------------------

				// Assign most significant (matching destination) image
				$ImagesData->imagePath = '';

				if ($ImagesData->IsOriginalImageFound)
				{
					$ImagesData->imagePath = $rsgConfig->get('imgPath_original') . '/' . $ImagesData->imageName;
				}

				if ($ImagesData->IsDisplayImageFound)
				{
					$ImagesData->imagePath = $rsgConfig->get('imgPath_display') . '/' . $ImagesData->imageName . '.jpg';
				}

				if ($ImagesData->IsThumbImageFound)
				{
					$ImagesData->imagePath = $rsgConfig->get('imgPath_thumb') . '/' . $ImagesData->imageName . '.jpg';
				}

				if ($ImagesData->IsWatermarkedImageFound)
				{
					//$ImagesData->imagePath = $rsgConfig->get('imgPath_watermarked') . '/' . $ImagesData->imageName; // . '.jpg';
					$ImagesData->imagePath = $rsgConfig->get('imgPath_watermarked') . '/' . $ImagesData->WatermarkedFileName; // . '.jpg';
				}

				$this->ImageReferenceList [] = $ImagesData;
			}
		}

		// Check watermarked
		foreach ($files_watermarked as $BaseFile)
		{
			$IsFileFound = false;

			foreach ($ValidWatermarkNames as $WatermarkName)
			{
				if ($WatermarkName == $BaseFile)
				{
					$IsFileFound = true;
					break;
				}
			}

			if (! $IsFileFound)
			{
				$ImagesData                 = new ImageReference($this->UseWatermarked);
				$ImagesData->imageName      = $BaseFile;
				$ImagesData->IsWatermarkedImageFound = true;

                $ImagesData->IsGalleryAssigned = false;

				$this->ImageReferenceList [] = $ImagesData;
			}
		}

		//--- Set column bits: Is one entry missing in column ? --------------------------------------

		$this->IsAnyImageMissingInDB = false;
		foreach ($this->ImageReferenceList as $ImageReference)
		{
			$this->IsAnyImageMissingInDB |= !$ImageReference->IsImageInDatabase;
		}

		$this->IsAnyImageMissingInDisplay = false;
		foreach ($this->ImageReferenceList as $ImageReference)
		{
			$this->IsAnyImageMissingInDisplay |= !$ImageReference->IsDisplayImageFound;
		}

		$this->IsAnyImageMissingInOriginal = false;
		foreach ($this->ImageReferenceList as $ImageReference)
		{
			$this->IsAnyImageMissingInOriginal |= !$ImageReference->IsOriginalImageFound;
		}

		$this->IsAnyImageMissingInThumb = false;
		foreach ($this->ImageReferenceList as $ImageReference)
		{
			$this->IsAnyImageMissingInThumb |= !$ImageReference->IsThumbImageFound;
		}

		$this->IsAnyOneImageMissing = false;
		foreach ($this->ImageReferenceList as $ImageReference)
		{
			// do not care as watermarked images are not missing as such. watermarked images will be created when displaying image
			$this->IsAnyOneImageMissing |= $ImageReference->IsMainImageMissing(ImageReference::dontCareForWatermarked);
		}

		$this->IsAnyImageMissingInWatermarked = false;

		if ($this->UseWatermarked)
		{
			foreach ($this->ImageReferenceList as $ImageReference)
			{
				$this->IsAnyImageMissingInWatermarked |= !$ImageReference->IsWatermarkedImageFound;
			}
		}

		return '';
	}

	/**
	 * @param int $ParentGalleryId
	 *
	 * @return string
	 *
	 * @since version 4.3
	 */
	private function getParentGalleryName($ParentGalleryId)
	{
		$ParentGalleryName = '???';

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('name'))
			->from($db->quoteName('#__rsgallery2_galleries'))
			->where('id = ' . $db->quote($ParentGalleryId));
		$db->setQuery($query);

		$DbGalleryName = $db->loadResult();
		if (!empty ($DbGalleryName))
		{
			$ParentGalleryName = $DbGalleryName;
		}

		return $ParentGalleryName;
	}

}