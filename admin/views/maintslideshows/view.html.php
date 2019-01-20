<?php
/**
 * @package     RSGallery2
 * @subpackage  com_rsgallery2
 * @copyright   (C) 2016-2018 RSGallery2 Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author      finnern
 * RSGallery is Free Software
 */

defined('_JEXEC') or die;

jimport('joomla.html.html.bootstrap');
jimport('joomla.application.component.view');
jimport('joomla.application.component.model');

require_once JPATH_COMPONENT_ADMINISTRATOR . '/includes/sidebarLinks.php';
JModelLegacy::addIncludePath(JPATH_COMPONENT . '/models');

/**
 * View of list of image discrepancies (missing images, Missing DB entries on images ...)
 *
 * @since 4.3.0
 */
class Rsgallery2ViewMaintSlideshows extends JViewLegacy
{

	protected $slidesConfig;

	//protected $formsSlides;
	protected $form2Maintain;
	protected $formUserSelectSlideshow;

	protected $slideshow2Maintain;

	//------------------------------------------------
	/**
	 * @param null $tpl
	 *
	 * @return mixed bool or void
	 * @since 4.3.0
	 */
	public function display($tpl = null)
	{
		global $Rsg2DevelopActive;
		global $rsgConfig;

		//---  ------------------------------------------
		//--- get user slide show name ------------------------------------------
		//---  ------------------------------------------

		$input = JFactory::getApplication()->input;

		// Slide show parameter saved -> name known
		$userSlideshow = $input->get('maintain_slideshow', "", 'STRING');
		$configSlideshow = $rsgConfig->get('current_slideshow');

			// Check rights of user
		$this->UserIsRoot = $this->CheckUserIsRoot();

		// collect slideshow names from existing folder
		$maintSlidesModel   = JModelLegacy::getInstance('MaintSlideshows', 'rsgallery2Model');
		$slideshowNames = $maintSlidesModel->collectSlideshowsNames();

		// use first, user selected or config slideshow name
		$this->userSlideshowName = $slideshowNames[1]; // May be ...parth
		if (in_array ($userSlideshow, $slideshowNames))
		{
			$this->userSlideshowName = $userSlideshow;
		}
		else
		{
			if (in_array ($configSlideshow, $slideshowNames))
			{
				$this->userSlideshowName = $configSlideshow;
			}
		}

		$xmlFile = JPATH_COMPONENT . '/models/forms/maintslideshows.xml';
		$this->formSlideshowSelection = JForm::getInstance('slideshowSelection', $xmlFile);

		// assign previous user selection
		$params = new JRegistry;
		$params->loadString("maintain_slideshow=" . $this->userSlideshowName);
		$this->formSlideshowSelection->bind($params);

		//---  ------------------------------------------
		//--- parameter form  ------------------------------------------
		//---  ------------------------------------------

		// $xmlFileInfo
		$this->slideConfigFile = $maintSlidesModel->collectSlideshowsConfigData(
			$this->userSlideshowName);


		//--- add parent form element ------------------------

		//--- add parameter values from xml file ------------------------

		$xmlForm = new SimpleXMLElement('<form></form>');
		if ( ! empty($this->slideConfigFile->formFields))
		{
			$this->SimpleXMLElement_append($xmlForm, $this->slideConfigFile->formFields->config->fields);
		}
		$formSlide = new JForm('slideParameter');

		$formSlide->load($xmlForm);

		$params = $this->slideConfigFile->parameterValues;

		$formSlide->bind($params);
		$this->formSlide = $formSlide;

		//--- begin of display --------------------------------------------

		$Layout = JFactory::getApplication()->input->get('layout');
		$this->addToolbar($this->UserIsRoot); //$Layout);

		$View = JFactory::getApplication()->input->get('view');
		RSG2_SidebarLinks::addItems($View, $Layout);

		$this->sidebar = JHtmlSidebar::render();
		/**/
		parent::display($tpl);

		return;
	}

	/**
	 * Checks if user has root status (is re.admin')
	 *
	 * @return    bool
	 * @since 4.3.0
	 */
	function CheckUserIsRoot()
	{
		$user     = JFactory::getUser();
		$canAdmin = $user->authorise('core.admin');

		return $canAdmin;
	}

	/**
	 * @param $UserIsRoot
	 *
	 * @since 4.3.0
	*/
	protected function addToolbar($UserIsRoot) //$Layout='default')
	{
		// save ??

		// on develop show open tasks if existing
		if (!empty ($Rsg2DevelopActive))
		{
			echo '<span style="color:red">Task: </span><br><br>';
		}

		/**/
		// Title
		JToolBarHelper::title(JText::_('COM_RSGALLERY2_MAINTENANCE') . ': ' . JText::_('COM_RSGALLERY2_MAINT_SLIDESHOW_CONFIG'), 'screwdriver');
		/**/

		if ($UserIsRoot)
		{
			JToolBarHelper::custom('maintslideshows.saveConfigParameter', 'equalizer', '', 'COM_RSGALLERY2_MAINT_SAVE_PARAMETER', false);
			JToolBarHelper::custom('maintslideshows.saveConfigFile', 'file', 'file', 'COM_RSGALLERY2_MAINT_SAVE_FILE', false);
			// JToolBarHelper::spacer();
		}
		/**/

		// back to maintenance
		JToolBarHelper::cancel('maintRegenerate.cancel');
	}

	function SimpleXMLElement_append($parent, $child)
	{
		// get all namespaces for document
		$namespaces = $child->getNamespaces(true);

		// check if there is a default namespace for the current node
		$currentNs = $child->getNamespaces();
		$defaultNs = count($currentNs) > 0 ? current($currentNs) : null;
		$prefix = (count($currentNs) > 0) ? current(array_keys($currentNs)) : '';
		$childName = strlen($prefix) > 1
			? $prefix . ':' . $child->getName() : $child->getName();

		// check if the value is string value / data
		if (trim((string) $child) == '') {
			$element = $parent->addChild($childName, null, $defaultNs);
		} else {
			$element = $parent->addChild(
				$childName, htmlspecialchars((string)$child), $defaultNs
			);
		}

		foreach ($child->attributes() as $attKey => $attValue) {
			$element->addAttribute($attKey, $attValue);
		}
		foreach ($namespaces as $nskey => $nsurl) {
			foreach ($child->attributes($nsurl) as $attKey => $attValue) {
				$element->addAttribute($nskey . ':' . $attKey, $attValue, $nsurl);
			}
		}

		// add children -- try with namespaces first, but default to all children
		// if no namespaced children are found.
		$children = 0;
		foreach ($namespaces as $nskey => $nsurl) {
			foreach ($child->children($nsurl) as $currChild) {
				$this->SimpleXMLElement_append($element, $currChild);
				$children++;
			}
		}
		if ($children == 0) {
			foreach ($child->children() as $currChild) {
				$this->SimpleXMLElement_append($element, $currChild);
			}
		}
	}


}


