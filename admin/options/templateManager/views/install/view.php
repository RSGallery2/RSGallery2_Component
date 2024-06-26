<?php
/**
 * @version        $Id: view.php 1011 2011-01-26 15:36:02Z mirjam $
 * @package        Joomla
 * @subpackage     Menus
 * @copyright      (C) 2005-2024 RSGallery2 Team
 * @license        GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die();

/**
 * RSGallery2 Template Manager Install View
 *
 * @package        Joomla
 * @subpackage     Installer
 * @since          1.5
 */

include_once(dirname(__FILE__) . '/../default/view.php');

/**
 * Class InstallerViewInstall
 */
class InstallerViewInstall extends InstallerViewDefault
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
		JToolBarHelper::help('screen.installer');

		$paths        = new stdClass();
		$paths->first = '';

		$this->paths = $paths;
		$this->state = $this->get('state');

		parent::showHeader();
		parent::display($tpl);
	}

}
