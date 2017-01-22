<?php
/**
 * @package     RSGallery2
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2016 - 2017 RSGallery2
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * RSGallery is Free Software
 */

defined('_JEXEC') or die;

// control center 
// ToDo:: rename to rsg_control and use as default ...

global $Rsg2DebugActive;

//$Rsg2DebugActive = false; // ToDo: $rsgConfig->get('debug');
if ($Rsg2DebugActive)
{
	// Include the JLog class.
	jimport('joomla.log.log');

	// identify active file
	JLog::add('==> ctrl.ctrl.upload.php ');
}

jimport('joomla.application.component.controllerform');

// ToDo: Check which functions are used

/**
 *
 *
 * @since 4.3.0
 */
class Rsgallery2ControllerUpload extends JControllerForm
{

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

}

