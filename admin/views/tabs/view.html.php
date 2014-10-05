<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class VipQuotesViewTabs extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

    protected $pageId;
    protected $pageName;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $this->state  = $this->get('State');

        /** @var  $params Joomla\Registry\Registry */
        $params = $this->state->get('params');
        $this->params = $params;

        $pageId = $this->state->get("page_id");
        if (!$pageId) {
            $msg = JText::_("COM_VIPQUOTES_ERROR_FACEBOOK_INVALID_PAGE");
            $app->redirect(JRoute::_("index.php?option=com_vipquotes&view=pages", false), $msg, "notice");

            return;
        }

        // Check for Facebook connect
        $facebook = new Facebook(array(
            'appId'      => $this->params->get("fbpp_app_id"),
            'secret'     => $this->params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));

        $facebookUserId = $facebook->getUser();
        if (!$facebookUserId) {
            $msg = JText::_("COM_VIPQUOTES_ERROR_FACEBOOK_NOT_CONNECT");
            $app->redirect(JRoute::_("index.php?option=com_vipquotes&view=pages", false), $msg, "notice");

            return;
        }

        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->pageId   = $pageId;
        $this->pageName = VipQuotesHelper::getFacebookPageName($pageId);

        // Prepare sorting data
        $this->prepareSorting();

        // Add submenu
        VipQuotesHelper::addSubmenu("pages");

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }


    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0) ? false : true;

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.published' => JText::_('JSTATUS'),
            'a.title'     => JText::_('COM_VIPQUOTES_TITLE'),
            'a.app_id'    => JText::_('COM_VIPQUOTES_APP_ID'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("archived" => false, "trash" => false)), 'value', 'text', $this->state->get('filter.state'), true)
        );

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolbarHelper::title(JText::sprintf('COM_VIPQUOTES_FACEBOOK_TABS', $this->pageName));
        JToolbarHelper::addNew('tab.add');
        JToolbarHelper::editList('tab.edit');
        JToolbarHelper::divider();
        JToolbarHelper::publishList("tabs.publish");
        JToolbarHelper::unpublishList("tabs.unpublish");
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_("COM_VIPQUOTES_DELETE_ITEMS_QUESTION"), "tabs.delete");

        // Back button
        JToolbarHelper::divider();
        JToolbarHelper::custom('pages.backToDashboard', "dashboard", "", JText::_("COM_VIPQUOTES_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::sprintf('COM_VIPQUOTES_FACEBOOK_TABS_ADMINISTRATION', $this->pageName));

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('itprism.ui.joomla_list');
    }
}
