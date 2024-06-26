<?php
/**
 * @version        $Id: extension.php 1012 2011-02-01 15:13:13Z mirjam $
 * @package        Joomla
 * @subpackage     Installer
 * @copyright      (C) 2005-2024 RSGallery2 Team
 * @license        GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * RSGallery2 Template Manager Abstract Extension Model
 *
 * @abstract
 * @package        Joomla
 * @subpackage     Installer
 * @since          1.5
 */
// ToDo deprecated: Fix undefined JModel
class InstallerModel extends JModelLegacy
{
	/** @var array Array of installed components */
	var $_items = array();

	/** @var object JPagination object */
	var $_pagination = null;

	/**
	 * Overridden constructor
	 *
	 * @access    protected
	 * @throws Exception
	 * @since 4.3.0
	 */
	function __construct()
	{
		$mainframe = JFactory::getApplication();

		// Call the parent constructor
		parent::__construct();

		// Set state variables from the request
		$this->setState('pagination.limit', $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->get('list_limit'), 'int'));
		$this->setState('pagination.offset', $mainframe->getUserStateFromRequest('com_rsgallery2_com_installer.limitstart.' . $this->_type, 'limitstart', 0, 'int'));
		$this->setState('pagination.total', 0);
	}

	/**
	 * @return array
	 * @since 4.3.0
	 */
	function &getItems()
	{
		if (empty($this->_items))
		{
			// Load the items
			$this->_loadItems();
		}

		return $this->_items;
	}

	/**
	 * @return JPagination|object
	 * @since 4.3.0
	 */
	function &getPagination()
	{
		if (empty($this->_pagination))
		{
			// Make sure items are loaded for a proper total
			if (empty($this->_items))
			{
				// Load the items
				$this->_loadItems();
			}
			// Load the pagination object
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->state->get('pagination.total'), $this->state->get('pagination.offset'), $this->state->get('pagination.limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Remove (uninstall) an extension
	 *
	 * @static
	 *
	 * @param    array $eid An array of identifiers
	 *
	 * @return    boolean    True on success
	 * @since 1.0
	 * @throws Exception
	 */
	function remove($eid = array())
	{
		$app = JFactory::getApplication();

		// Initialize variables
		$failed = array();

		/*
		 * Ensure eid is an array of extension ids in the form id => client_id
		 * TODO: If it isn't an array do we want to set an error and fail?
		 */
		if (!is_array($eid))
		{
			$eid = array($eid => 0);
		}

		// Get a database connector
		$db = JFactory::getDBO();

		// Get an installer object for the extension type
		jimport('joomla.installer.installer');
		$installer = &JInstaller::getInstance();

		require_once(rsgOptions_installer_path . '/adapters/rsgtemplate.php');
		$installer->setAdapter('template', new JInstaller_rsgTemplate($installer));

		// Uninstall the chosen extensions
		foreach ($eid as $id => $clientId)
		{
			$id     = trim($id);
			$result = $installer->uninstall($this->_type, $id, $clientId);

			// Build an array of extensions that failed to uninstall
			if ($result === false)
			{
				$failed[] = $id;
			}
		}

		if (count($failed))
		{
			// There was an error in uninstalling the package
			$msg    = JText::sprintf('COM_RSGALLERY2_UNINSTALLEXT', JText::_($this->_type), JText::_('COM_RSGALLERY2_ERROR'));
			$result = false;
		}
		else
		{
			// Package uninstalled sucessfully
			$msg    = JText::sprintf('COM_RSGALLERY2_UNINSTALLEXT', JText::_($this->_type), JText::_('COM_RSGALLERY2_SUCCESS'));
			$result = true;
		}

		$app->enqueueMessage($msg);
		$this->setState('action', 'remove');
		$this->setState('name', $installer->get('name'));
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));

		return $result;
	}

	/**
	 * @return object
	 * @since 4.3.0
	 */
	function _loadItems()
	{
		JFactory::getApplication()->enqueueMessage('RSGallery2 _loadItems:<pre>'
			. JText::_('COM_RSGALLERY2_METHOD_NOT_IMPLEMENTED') . '</pre>', 'error');

        return false;
	}
}
