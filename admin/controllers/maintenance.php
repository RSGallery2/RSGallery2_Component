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

global $Rsg2DebugActive;

if ($Rsg2DebugActive)
{
	// Include the JLog class.
	jimport('joomla.log.log');

	// identify active file
	JLog::add('==> ctrl.maintenanc.php ');
}

jimport('joomla.application.component.controlleradmin');

/**
 * some more general functions for maintenance
 *
 * @since 4.3.0
 */
class Rsgallery2ControllerMaintenance extends JControllerAdmin
{

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
     *
	 * @since 4.3
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

    /**
     * Move to maintenance main page on cancel
     * May be issued from other sub forms like maintconsolidatedb
     *
     * @since 4.3
     */
	public function Cancel()
	{
		/*
		global $Rsg2DebugActive;

		if($Rsg2DebugActive)
		{
			JLog::add('==> ctrl.maintenance.php/function Cancel');
		}

		$msg = 'All RSG2 Images and thumbs are deleted. ';
		// $app->redirect($link, $msg, $msgType='message');
		*/
		$msg     = '';
		$msgType = 'notice';

		// ToDo: Use Jroute before link for setRedirect :: check all apperances
		$this->setRedirect('index.php?option=com_rsgallery2&view=maintenance', $msg, $msgType);
	}

    /**
     * Delete RSGallery language files not inside RSG2 component
     * recursive files search in backend folder
     *
     * @since 4.3
     */
	function delete_base_LangFiles()
	{
		$msg     = "Delete base language files: ";
		$msgType = 'notice';

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Access check
		$canAdmin = JFactory::getUser()->authorise('core.admin', 'com_rsgallery2');
		if (!$canAdmin)
		{
			$msg     = $msg . JText::_('JERROR_ALERTNOAUTHOR');
			$msgType = 'warning';
			// replace newlines with html line breaks.
			$msg = nl2br ($msg);
		}
		else
		{
			try
			{
				// .../administrator/language/
				$startDir  = JPATH_ADMINISTRATOR . '/language';
				$IsDeleted = $this->findAndDelete_RSG2_LangFiles($startDir, 'Backend: ');
				if ($IsDeleted)
				{
					$msg .= ' Backend successful';
				}
				else
				{
					$msg .= ' Backend with error';
					$msgType = 'error';
				}

				// .../administrator/language/
				$startDir  = JPATH_SITE . '/language';
				$IsDeleted = $this->findAndDelete_RSG2_LangFiles($startDir, 'Site: ');
				if ($IsDeleted)
				{
					$msg .= ', Site  successful';
				}
				else
				{
					$msg .= ' Site with error';
					$msgType = 'error';
				}


			}
			catch (RuntimeException $e)
			{
				$OutTxt = '';
				$OutTxt .= 'Error executing delete_base_LangFiles: "' . '<br>';
				$OutTxt .= 'Error: "' . $e->getMessage() . '"' . '<br>';

				$app = JFactory::getApplication();
				$app->enqueueMessage($OutTxt, 'error');
			}
		}
		$this->setRedirect('index.php?option=com_rsgallery2&view=maintenance', $msg, $msgType);
	}

	/**
     * Delete RSGallery language files not inside RSG2 comüponent
     * recursive files search in starting folder
     *
     * @param $startDir Example: \administrator\language\
	 * @return bool True on delete successful False otherwise
	 *
	 * @since 4.3
	 */
	function findAndDelete_RSG2_LangFiles($startDir, $title)
	{
		$IsDeleted = false;

		if ($startDir != '')
		{
			//  code...
			// ...\en-GB\en-GB.com_rsgallery2.ini
			// ...\en-GB\en-GB.com_rsgallery2.sys.ini

			$Directories = new RecursiveDirectoryIterator($startDir, FilesystemIterator::SKIP_DOTS);
			$Files       = new RecursiveIteratorIterator($Directories);
			$LangFiles   = new RegexIterator($Files, '/^.+\.com_rsgallery2\..*ini$/i', RecursiveRegexIterator::GET_MATCH);

			//$msg         = $title . '<br>';
			$msg= '';
			$IsFileFound = false;
			foreach ($LangFiles as $LangFile)
			{
				$IsFileFound = true;

				$msg .= '<br>' . $LangFile[0];
				$IsDeleted = unlink($LangFile[0]);
				if ($IsDeleted)
				{
					$msg .= ' is deleted';

				}
				else
				{
					$msg .= ' is not deleted';
				}
			}

			// One or more files found ?
			if ($IsFileFound)
			{
				// $IsDeleted = true;
				$msg = $title . '<br>';
				'Found files: ' . $msg;
			}
			else
			{
				$msg = $title . '<br>' . 'OK: No files needed to be deleted: ';
				$IsDeleted = True;
			}

			JFactory::getApplication()->enqueueMessage($msg, 'notice');
		}

		return $IsDeleted;
	}

	/**
	 *
	 *
	 * @since 4.4.2
	 */
	function repairImagePermissions()
	{
		//$msg     = "repairImagePermissions: ";
		$msg = "Repaired image permissions: <br>";
		$msgType = 'notice';

		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Access check
		$canAdmin = JFactory::getUser()->authorise('core.manage', 'com_rsgallery2');
		if (!$canAdmin)
		{
			$msg     = $msg . JText::_('JERROR_ALERTNOAUTHOR');
			$msgType = 'warning';
			// replace newlines with html line breaks.
			$msg = nl2br ($msg);
		}
		else
		{
			//--- Delete all images -------------------------------

			try
			{
				$imageModel = $this->getModel('MaintImageFiles');
				$msg        .= $imageModel->repairImagePermissions();
			}
			catch (RuntimeException $e)
			{
				$OutTxt = '';
				$OutTxt .= 'Error executing repairImagePermissions: "' . '<br>';
				$OutTxt .= 'Error: "' . $e->getMessage() . '"' . '<br>';

				$app = JFactory::getApplication();
				$app->enqueueMessage($OutTxt, 'error');
			}
		}

		$this->setRedirect('index.php?option=com_rsgallery2&view=maintenance', $msg, $msgType);
	}


} // class


