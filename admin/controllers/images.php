<?php
defined('_JEXEC') or die;

/*
global $Rsg2DebugActive;

if ($Rsg2DebugActive)
{
	// Include the JLog class.
	jimport('joomla.log.log');

	// identify active file
	JLog::add('==> ctrl.image.php ');
}
/**/

jimport('joomla.application.component.controlleradmin');

class Rsgallery2ControllerImages extends JControllerAdmin
{

	public function getModel($name = 'Image', 
 							 $prefix = 'Rsgallery2Model', 
  							 $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

}