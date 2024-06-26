<?php
/**
 * @package        RSGallery2
 * @subpackage     TemplateManager
 * @copyright      (C) 2005-2024 RSGallery2 Team
 * @license        GNU/GPL, see LICENSE.php
 */


defined('_JEXEC') or die;

// Import library dependencies
require_once(dirname(__FILE__) . '/extension.php');
jimport('joomla.filesystem.folder');

/**
 * RSGallery2 Template Manager Template Model
 *
 * @package        RSGallery2
 * @subpackage     TemplateManager
 * @since          1.5
 */
class InstallerModelSelectCss extends InstallerModel
{
	/**
	 * Extension Type
	 *
	 * @var    string
	 */
	var $_type = 'SelectCss';

	/**
	 * Overridden constructor
	 *
	 * @access    protected
	 * @since 4.3.0
	 */
	function __construct()
	{
		$mainframe = JFactory::getApplication();

		// Call the parent constructor
		parent::__construct();

		// Set state variables from the request
		$this->setState('filter.string', $mainframe->getUserStateFromRequest("com_rsgallery2_com_installer.templates.string", 'filter', '', 'string'));
	}

	/**
	 * @return stdClass
	 * @since 4.3.0
	 */
	function getItems()
	{
		// Determine template CSS directory
		$dir = JPATH_RSGALLERY2_SITE . '/templates'. '/' .$this->template . '/css';

		// List template .css files
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($dir, '\.css$', false, false);

		$this->_items           = new stdClass();
		$this->_items->template = $this->template;
		$this->_items->dir      = $dir;
		$this->_items->files    = $files;

		return $this->_items;
	}
}
