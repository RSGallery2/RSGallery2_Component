<?php

defined( '_JEXEC' ) or die;

jimport ('joomla.html.html.bootstrap');
jimport('joomla.application.component.view');

//require (JUri::root(true).'/administrator/components/com_rsgallery2/helpers/CreditsEnumaration.php');
//require ('helpers/CreditsEnumaration.php');
//require_once JPATH_ADMINISTRATOR . '/components/com_rsgallery2/helpers/rsg2Common.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsgallery2/includes/version.rsgallery2.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsgallery2/helpers/CreditsEnumaration.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsgallery2/models/images.php';
require_once JPATH_ADMINISTRATOR . '/components/com_rsgallery2/models/galleries.php';

class Rsgallery2ViewRsgallery2 extends JViewLegacy
{
	protected $Credits;
	// ToDo: Use other rights instead of core.admin -> IsRoot ?
	// core.admin is the permission used to control access to 
	// the global config
	protected $UserIsRoot; 
	protected $LastImages;
	protected $LastGalleries;
	protected $Rsg2Version;
	
	protected $FootetText;
	
	protected $form;
	protected $sidebar;

	//------------------------------------------------
	public function display ($tpl = null)
	{
		//--- get needed data ------------------------------------------
		
		// List of credits for rsgallery2 developers / translators
		$this->Credits = CreditsEnumaration::CreditsEnumarationText;
	
		// Check rights of user
		$this->UserIsRoot = $this->CheckUserIsRoot ();

		// fetch data of last galleries (within one week ?)
        //$this->LastImages = rsg2ModelImages::lastWeekImages(5);
        $this->LastImages = rsgallery2ModelImages::latestImages(5);

        //$this->LastGalleries = rsg2ModelGalleries::lastWeekGalleries(5);
        $this->LastGalleries = rsgallery2ModelGalleries::latestGalleries(5);

		// Get rsgallery2 component version 
		// $this->Rsg2Version = rsg2Common::getRsg2ComponentVersion();
		//$this->Rsg2Version = rsg2Common::getRsg2ComponentVersion();
		$Rsg2Version = new rsgalleryVersion();
		
		$this->Rsg2Version = $Rsg2Version->getLongVersion(); // getShortVersion, getVersionOnly				
		$this->FootetText = $this->RSGallery2Footer ($Rsg2Version->getCopyrightVersion());
		
		$form = $this->get('Form');
		
		//--- begin to display --------------------------------------------

		
/*		// Options button.
		if ($this->UserIsRoot) {
			JToolBarHelper::preferences('com_rsgallery2');
		}
*/
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Assign the Data
		$this->form = $form;
        	
		//$this->addToolbar ();
		JToolBarHelper::title(JText::_('COM_RSGALLERY2_MENU_CONTROL_PANEL'), 'config');
		$this->sidebar = JHtmlSidebar::render ();

		parent::display ($tpl);

        return;
	}

	/**
	 * Checks if user has root status (is re.admin')
	 *
	 * @return	bool
	 */		
	function CheckUserIsRoot ()
	{
		$user = JFactory::getUser();
		$canAdmin = $user->authorise('core.admin');
		return $canAdmin;
	}	
	
    /**
     * Method to set up the document properties
     *
     * @return void
     *
    protected function setDocument() 
    {
            $document = JFactory::getDocument();
            $document->setTitle(JText::_('COM_RSGALLERY2_MENU_CONTROL_PANEL'));
    }
	*/
	


    /**
     * Inserts the HTML placed at the bottom of (all) RSGallery Admin pages.
     */
    function RSGallery2Footer($rsg2ShortVersion){
		
		$Footer = <<<EOD
        <div class= "rsg2-footer" align="center"><br /><br />$rsg2ShortVersion</div>
        <div class='rsg2-clr'>&nbsp;</div>
EOD;
		return $Footer;
    }

}	
