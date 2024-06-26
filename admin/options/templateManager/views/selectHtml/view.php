<?php
/**
 * @version        $Id: view.php 1011 2011-01-26 15:36:02Z mirjam $
 * @package        RSGallery2
 * @subpackage     Template installer
 * @copyright      (C) 2005-2024 RSGallery2 Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die();

/**
 * RSGallery2 Template Manager Templates View
 *
 * @package        RSGallery2
 * @subpackage     Template installer
 * @since          1.5
 */

include_once(dirname(__FILE__) . '/../default/view.php');

/**
 * Class InstallerViewSelectHtml
 */
class InstallerViewSelectHtml extends InstallerViewDefault
{
	/**
	 * @param null $tpl
	 * @since 4.3.0
	 */
	function display($tpl = null)
	{
		/*
		 * Set toolbar items for the page
		 */
		// JToolBarHelper::editHtmlX( 'editHTML'); J3
		JToolBarHelper::editHtml('editHTML');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('manage');
		JToolBarHelper::help('screen.installerSelectHtml');

		// Get data from the model
		$item       = $this->get('Items');
		$this->item = $item;

		parent::showTemplateHeader();
		parent::display($tpl);
	}

}
