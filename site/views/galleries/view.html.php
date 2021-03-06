<?php
/**
 * @package    [PACKAGE_NAME]
 *
 * @author     [AUTHOR] <[AUTHOR_EMAIL]>
 * @copyright  [COPYRIGHT]
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       [AUTHOR_URL]
 */

use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Foo view.
 *
 * @package  [PACKAGE_NAME]
 * @since    1.0
 */
class RSGallery2ViewGalleries extends HtmlView
{
	/**
	 * Display job item
	 *
	 * @param   string  $tpl  template name
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		echo "RSGallery2ViewGalleries (Overview)<br />";

		/**/
		// Get gallery data for the view
		$this->items = $this->get('Items');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new RuntimeException(implode('<br />', $errors), 500);
		}
		/**/

		// Display the view
		parent::display($tpl);
	}
}
