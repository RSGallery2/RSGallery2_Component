<?php
/**
 * @package       RSGallery2
 * @copyright (C) 2003-2024 RSGallery2 Team
 * @license       http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * RSGallery is Free Software
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.html.html.bootstrap');

//require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/RSGallery2.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/includes/sidebarLinks.php';

/**
 * View class for a list of galleries
 *
 * @since 4.3.0
 */
class Rsgallery2ViewGalleries extends JViewLegacy
{
	// ToDo: Use other rights instead of core.admin -> IsRoot ?
	// core.admin is the permission used to control access to 
	// the global config

	protected $UserIsRoot;
	protected $sidebar;

	protected $items;
	protected $pagination;
	protected $state;

	protected $dbOrdering;

//	protected $rsgConfigData;

	//------------------------------------------------
	/**
	 * @param null $tpl
	 *
	 * @since 4.3.0
	*/
	public function display($tpl = null)
	{
		global $Rsg2DevelopActive;

		//--- get needed form data ------------------------------------------

		// Check rights of user
		$this->UserIsRoot = $this->CheckUserIsRoot();

//		global $rsgConfig;
//		$this->rsgConfigData = $rsgConfig;

		$this->items = $this->get('Items');

		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Actual List to order all galleries
        $gOrderingModel = JModelLegacy::getInstance('galleriesorder', 'rsgallery2Model');
        $this->dbOrdering = $gOrderingModel->OrderedGalleries();
//        echo '$OrderedGalleries: ' . json_encode($this->dbOrdering) . '<br>';
//        echo ('<br>Length(count): ' . count($this->dbOrdering) . '<br>');

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

		// different toolbar on different layouts
		$Layout = JFactory::getApplication()->input->get('layout');
		$this->addToolbar($Layout);

        $View = JFactory::getApplication()->input->get('view');
        RSG2_SidebarLinks::addItems($View, $Layout);
//        RSGallery2Helper::addSubmenu('rsgallery2');
		$this->sidebar = JHtmlSidebar::render();

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
	 * @param string $Layout
	 *
	 * @since 4.3.0
	*/

	protected function addToolbar($Layout = 'default')
	{
	    global $Rsg2DevelopActive;
	    
		switch ($Layout)
		{
			case 'galleries_raw':
				JToolBarHelper::title(JText::_('COM_RSGALLERY2_GALLERIES_VIEW_RAW_DATA'), 'images');

				JToolBarHelper::editList('gallery.edit');
//				JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'gallery.delete', 'JTOOLBAR_EMPTY_TRASH');

				// on develop show open tasks if existing
				if (!empty ($Rsg2DevelopActive))
				{
					echo '<span style="color:red">'
						. 'Tasks: <br>'
						. '* Add delete function, Test add double name<br>'
						. '* Add trash state of gallery, delete only if trashed<br>'
						//. '* <br>'
						//. '* <br>'
						//. '* <br>'
						. '</span><br><br>';
				}
				break;

			default:
				JToolBarHelper::title(JText::_('COM_RSGALLERY2_MANAGE_GALLERIES'), 'images');

				JToolBarHelper::addNew('gallery.add');
				JToolBarHelper::editList('gallery.edit');
//				JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'galleries.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::deleteList('', 'galleries.delete', 'JTOOLBAR_DELETE');

				JToolbarHelper::publish('galleries.publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('galleries.unpublish', 'JTOOLBAR_UNPUBLISH', true);

                // on develop show open tasks if existing
                if (!empty ($Rsg2DevelopActive))
                {
                    echo '<span style="color:red">'
	                    . 'Tasks: <br>'
	                    . '* c) Search tools -> group by parent/ parent child tree<br>'
	                    . '* Add trash state of gallery, delete only if trashed<br>'
	                    //. '* <br>'
	                    //. '* <br>'
	                    //. '* <br>'
	                    //. '* <br>'
	                    . '</span><br><br>';
                }

                break;
		}

	}
}


