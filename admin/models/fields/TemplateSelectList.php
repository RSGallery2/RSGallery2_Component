<?php
/*
* @package RSGallery2
* @copyright (C) 2005-2024 RSGallery2 Team
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery2 is Free Software
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 *  Select slideshow List Form Field class to create contents of dropdown box for
 * usable grafic libraries on the system
 *
 * @since 4.3.0
 */
class JFormFieldTemplateSelectList extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var string
     *
     * @since 4.3.0
     */
	protected $type = 'TemplateSelectList';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  string array  An array of JHtml options.
     *
     * @since 4.3.0
	 */
	protected function getOptions()
	{
		$current_slideshows = array();

		try
		{
			/**
			 * Detect available slideshows
			 * Search in source folders
			 */

			// Format values for slideshow dropdownbox
			$folders = JFolder::folders(JPATH_RSGALLERY2_SITE . '/templates');
			foreach ($folders as $folder)
			{
				//if (preg_match("/slideshow/i", $folder))
				//if (preg_match("//i", $folder)) // ^((?!hede).)*$
				if (strpos($folder, 'slideshow') === false && strpos($folder, 'meta') === false)
				{
					$current_slideshows[] = JHtml::_("select.option", $folder, $folder);
				}
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage());
		}

        // Merge any additional options in the XML definition.
        // $options[] = JHtml::_('select.option', $key, $value);

        $options = array_merge(parent::getOptions(), $current_slideshows);

		return $options;
	}

}

