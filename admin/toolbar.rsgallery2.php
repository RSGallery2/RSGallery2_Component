<?php
/**
 * RSGallery2 Toolbar Menu
 *
 * @version       $Id: toolbar.rsgallery2.php 1056 2012-01-01 17:39:14Z mirjam $
 * @package       RSGallery2
 * @copyright (C) 2003-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

// ensure this file is being included by a parent file
defined('_JEXEC') or die();

global $rsgOption;
/*
// RSG2 is a meta component.  joomla calls components options, RSG2 calls it's components rsgOptions
if( isset( $_REQUEST['rsgOption'] ))
    $rsgOption = $_REQUEST['rsgOption'];
else
    $rsgOption = '';
/**/
// $rsgOption is already set. Make ready for switch statement
if (empty ($rsgOption))
{
	$rsgOption = '';
}

require_once(JPATH_COMPONENT_ADMINISTRATOR . '/toolbar.rsgallery2.html.php');///J3

//-----------------------------------------------
// Only show RSG2 submenu in the backend
//$app = JFactory::getApplication();
//if ($app->isAdmin()){
$canManage = JFactory::getUser()->authorise('core.manage', 'com_rsgallery2');
if ($canManage)
{
	menu_rsg2_submenu::addRSG2Submenu($rsgOption, $task, $view, $layout);
	switch ($rsgOption)
	{
		case 'images':
			switch ($task)
			{
				case 'new':
				case 'edit':
				case 'editA':
					menu_rsg2_images::edit($option);
					break;
				case 'remove':
					menu_rsg2_images::remove($option);
					break;
				case 'upload':
					menu_rsg2_images::upload($option);
					break;
				case 'batchupload':
					menuRSGallery::image_batchUpload();
					break;
				case 'save_batchupload':
					menuRSGallery::images_show();
					break;
				default:
					menu_rsg2_images::show($option);
					break;
			}
			break;

		case 'galleries':
			switch ($task)
			{
				case 'new':
				case 'add':
				case 'edit':
				case 'editA':
					menu_rsg2_galleries::edit($option);
					break;
				case 'remove':
					menu_rsg2_galleries::remove($option);
					break;
				default:
					menu_rsg2_galleries::show($option);
					break;
			}
			break;

		case 'templateManager':
			switch ($task)
			{
				case 'view'   :
				case 'preview':
					menu_rsg2_templateManager::_VIEW();
					break;
				case 'edit_source':
				case 'edit_display':
					menu_rsg2_templateManager::_EDIT_SOURCE();
					break;
				case 'edit':
					menu_rsg2_templateManager::_EDIT();
					break;
				case 'choose_css':
					menu_rsg2_templateManager::_CHOOSE_CSS();
					break;
				case 'edit_css':
					menu_rsg2_templateManager::_EDIT_CSS();
					break;
				case 'choose_override':
					menu_rsg2_templateManager::_CHOOSE_OVERRIDE();
					break;
				case 'edit_override':
					menu_rsg2_templateManager::_EDIT_OVERRIDE();
					break;
				case 'doInstall':
				case 'showInstall':
					menu_rsg2_templateManager::_INSTALL();
					break;
				default:
					menu_rsg2_templateManager::_DEFAULT();
					break;
			}
			break;

		case 'maintenance':
			switch ($task)
			{
				case 'regenerateThumbs':
					menu_rsg2_maintenance::regenerateThumbs($option);
					break;
				default:
					menuRSGallery::simple();
					break;
			}
			break;

		case 'config':
			switch ($task)
			{
				case 'applyConfig':
				case 'showConfig':
					menuRSGallery::config_show();
					break;
			}
			break;

		case 'jumploader':
			switch ($task)
			{
				case 'showUpload':
					menu_rsg2_jumploader::show();
					break;
				default:
					menu_rsg2_jumploader::simple();
					break;
			}
	}// end switch( $rsgOption )

	// only use the legacy task switch if rsgOption is not used.
	if ($rsgOption == '')
	{
		switch ($task)
		{
			case "new":
				menuRSGallery::image_new();
				break;

			case "edit_image":
				menuRSGallery::image_edit();
				break;

			case "upload":
				menuRSGallery::image_upload();
				break;

			case "delete_image":
			case "move_image":
			case "save_image":
			case "view_images":
			case "images_orderup":
			case "images_orderdown":
				menuRSGallery::images_show();
				break;

			case 'applyConfig':
			case "showConfig":
				menuRSGallery::config_show();
				break;

			case 'config_rawEdit_apply':
			case 'config_rawEdit':
				menuRSGallery::config_rawEdit();
				break;
			case 'config_dumpVars':
				menuRSGallery::config_dumpVars();
				break;

			case 'edit_css':
				menuRSGallery::edit_css();
				break;
			case 'edit_main':
				menuRSGallery::edit_main();
				break;
			case 'edit_thumbs':
				menuRSGallery::edit_main();
				break;
			case 'edit_display':
				menuRSGallery::edit_main();
				break;
			// this is where you should add more toolbars:

			// do these need a toolbar?:
			case 'regen_thumbs':
			case 'import_captions':
			case 'consolidate_db_go':
			case 'consolidate_db':
			case 'install':
				break;

			// the following options either bring you to the control panel or only need a help button
			default:
			case 'controlPanel':
			case 'config_rawEdit_save':
			case 'migration':
			case 'purgeEverything':
			case 'saveConfig':
			case 'viewChangelog':
				// menuRSGallery::simple();
				break;
		} // switch task
	} // only use the legacy task switch if rsgOption is not used.
}    // Only show RSG2 submenu in the backend - end
