<?php
/**
 * @package     RSGallery2
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2016-2024 RSGallery2 Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * RSGallery is Free Software
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 *
 *
 * @since 4.3.0
 */
class JFormFieldRsgImageOrderingList extends JFormFieldList
{

	protected $type = 'RsgImageOrderingList';

	/**
	 * Method to get the field options. -> List of galleries
	 *
	 * @return  array  The field option objects
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = array();

		$GalleryId = $this->form->getValue('gallery_id');
		$DbVarName = (string) $this->element['name'];

		// List of row number to image names
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($DbVarName . ' As idx, name As text')
			->from('#__rsgallery2_files AS a')
			->where('gallery_id =' . (int) $GalleryId)
			->order('a.' . $DbVarName);

		// Get the options.
		$db->setQuery($query);

		try
		{
			$images = $db->loadObjectList();

			// Create row number to Text = "Row number -> image name" assignment
			foreach ($images as $image)
			{
				$options[] = array("value" => $image->idx, "text" => str_pad($image->idx, 3, " ", STR_PAD_LEFT) . ' ->' . $image->text);
			}
		}
		catch (RuntimeException $e)
		{
            JFactory::getApplication()->enqueueMessage($e->getMessage());
		}

        // Add JGLOBAL_NEWITEMSLAST_DESC
        //if (count($options) == 0)
        //{
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);
        //}

		return $options;
	}
}
