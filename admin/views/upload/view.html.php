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

jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

JModelLegacy::addIncludePath(JPATH_COMPONENT . '/models');

//require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/RSGallery2.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/includes/sidebarLinks.php';

/**
 * View class for uploading images
 *
 * @since 4.3.0
 */
class Rsgallery2ViewUpload extends JViewLegacy
{
    protected $form;
    protected $sidebar;

    // [Single images], [Zip file], [local server (ftp) path],
    protected $ActiveSelection; // ToDo: Activate in html of view

    protected $UploadLimit;
    protected $PostMaxSize;
    protected $MemoryLimit;
    protected $MaxSize;

    protected $FtpUploadPath;
    // protected $LastUsedUploadZip;
    protected $is1GalleryExisting;

    //------------------------------------------------
    /**
     * Prepare view
     *
     * @param null $tpl
     *
     * @return bool
     * @since 4.3.0
     * @throws Exception
	 */
    public function display($tpl = null)
    {
        global $Rsg2DevelopActive, $rsgConfig;

        $xmlFile = JPATH_COMPONENT . '/models/forms/upload.xml';
        $form = JForm::getInstance('upload', $xmlFile);

        // Instantiate the media helper
        $mediaHelper = new JHelperMedia;
        // Maximum allowed size in MB
        $this->UploadLimit = round($mediaHelper->toBytes(ini_get('upload_max_filesize')) / (1024 * 1024));
        $this->PostMaxSize = round($mediaHelper->toBytes(ini_get('post_max_size')) / (1024 * 1024));
        $this->MemoryLimit = round($mediaHelper->toBytes(ini_get('memory_limit')) / (1024 * 1024));
        $this->MaxSize = JFilesystemHelper::fileUploadMaxSize();

        //--- FtpUploadPath ------------------------

        //$UploadModel = JModelLegacy::getInstance('upload', 'rsgallery2Model');

        // Retrieve path from config
        $FtpUploadPath = $rsgConfig->get('ftp_path');
        // On empty use last successful
        if (empty ($FtpUploadPath)) {
            $FtpUploadPath = $rsgConfig->getLastUsedFtpPath();
        }
        $this->FtpUploadPath = $FtpUploadPath;

        //--- LastUsedUploadZip ------------------------

        // Not possible to set input variable in HTML so it is not collected
        // $this->LastUploadedZip = $app->getUserState('com_rsgallery2.last_used_uploaded_zip');
        // $LastUsedUploadZip->getLastUsedUploadZip();

        //--- Config requests ------------------------

        // register 'upload_drag_and_drop', 'upload_zip_pc', 'upload_folder_server'
        //$this->ActiveSelection = $rsgConfig->getLastUpdateType();
        $this->ActiveSelection = $rsgConfig->last_update_type;
        if (empty ($this->ActiveSelection)) {
            $this->ActiveSelection = 'upload_drag_and_drop';
        }

        // 0: default, 1: enable, 2: disable
        $isUseOneGalleryNameForAllImages = $rsgConfig->get('isUseOneGalleryNameForAllImages');
        if (empty ($isUseOneGalleryNameForAllImages)) {
            $isUseOneGalleryNameForAllImages = '1';
        }
        if ($isUseOneGalleryNameForAllImages == '2') {
            $isUseOneGalleryNameForAllImages = '0';
        }

        //--- Pre select latest gallery ?  ------------------------

        $IdGallerySelect = -1; //No selection

        $input = JFactory::getApplication()->input;

        // coming from gallery edit -> new id
        $Id = $input->get('id', 0, 'INT');
        if (!empty ($Id)) {
            $IdGallerySelect = $Id;
        }

        $isPreSelectLatestGallery = $rsgConfig->getIsPreSelectLatestGallery();
        if ($isPreSelectLatestGallery) {
            $IdGallerySelect = $this->getIdLatestGallery();
        }

        $this->is1GalleryExisting = $this->is1GalleryExisting();

        // upload_zip, upload_folder
        $formParam = array(
            'all_img_in_step1_01' => $isUseOneGalleryNameForAllImages,
            'all_img_in_step1_02' => $isUseOneGalleryNameForAllImages,
            'SelectGalleries01_01' => $IdGallerySelect,
            'SelectGalleries02_02' => $IdGallerySelect
        );

        $form->bind($formParam);

		// 2020.10.28 php 7.2 -> 7.4
        //// Check for errors.
        //if (count($errors = $this->get('Errors')))
        //{
        //    throw new RuntimeException(implode('<br />', $errors), 500);
        //}

        // Check for errors.
		if ($errors = $this->get('Errors'))
		{
			if (count($errors))
			{
				throw new RuntimeException(implode('<br />', $errors), 500);
			}
		}

	    // Assign the Data
        $this->form = $form;
        // $this->item = $item;

        // different toolbar on different layouts
        $Layout = JFactory::getApplication()->input->get('layout');
        $this->addToolbar($Layout);

	    $View = JFactory::getApplication()->input->get('view');
	    RSG2_SidebarLinks::addItems($View, $Layout);

        $this->sidebar = JHtmlSidebar::render();

        return parent::display($tpl);
    }

	/**
	 * @param string $Layout
	 *
	 * @since 4.3.0
	*/
    protected function addToolbar($Layout = 'default')
    {
        global $Rsg2DevelopActive;

        // on develop show open tasks if existing
        if (!empty ($Rsg2DevelopActive))
        {
	        echo '<span style="color:red">'
                . 'Tasks: <br>'
				. '* ??? Upload does not display image ???<br>'
				. '* !!! Upload does not create thumb image !!!<br>'
		        . '* !!! Not image files like dummy.htm -> check image size and stop on create db entry !!! <br>'
				. '* !!! Characters in Filenames not allowed like spaces ... -> can\'t be downloaded when  ... is in it !!!<br>'
				. '* !!! Test filenames with blanks and other ... '
				. '* Config -> update gallery selection preselect last used gallery ? show combo opened for n entries <br>'
				. '* Config -> update gallery selection preselect latest gallery  (User input ...) <br>'
	            . '* Hide "Drag images here" ... after first successful upload<br>'
	            . '* Hide progressBar when image appears '
                . '* Do documentation of model calling order and improve calling as is slow<br>'
//				. '*  <br>'
//				. '*  <br>'
//				. '*  <br>'
                . '</span><br><br>';
        }

        switch ($Layout) {
            case 'test':

                break;

            default:
                // COM_RSGALLERY2_SPECIFY_UPLOAD_METHOD
                JToolBarHelper::title(JText::_('COM_RSGALLERY2_SUBMENU_UPLOAD'), 'generic.png');
                break;
        }
    }


    /**
     * Query for ID of latest gallery
     *
     * @return string ID of latest gallery
     *
     * @since 4.3.0
     */
    public function getIdLatestGallery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('id'))
            ->from('#__rsgallery2_galleries')
            ->order('ordering ASC');
//			->setLimit(1);  ==>  setQuery($query, $offset = 0, $limit = 0)

        $db->setQuery($query, 0, 1);
        $IdLatestGallery = $db->loadResult();

        return $IdLatestGallery;
    }

    /**
     * Check if at least one gallery exists
     *
     * @return string ID of latest gallery
     *
     * @since 4.3.0
     */
    public function is1GalleryExisting()
    {
        $is1GalleryExisting = false;

        // ToDo: try
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // ToDo gew row number instead of names
        $query->select($db->quoteName('id'))
            ->from('#__rsgallery2_galleries');

        $db->setQuery($query, 0, 1);
        $IdGallery = $db->loadResult();
        $is1GalleryExisting = !empty ($IdGallery);

        return $is1GalleryExisting;
    }
}

