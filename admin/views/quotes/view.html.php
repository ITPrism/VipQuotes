<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class VipQuotesViewQuotes extends JViewLegacy
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

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        JLoader::register('JHtmlString', JPATH_LIBRARIES . '/joomla/html/html/string.php');

        // Prepare sorting data
        $this->prepareSorting();

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
            'a.ordering'  => JText::_('JGRID_HEADING_ORDERING'),
            'a.published' => JText::_('JSTATUS'),
            'a.quote'     => JText::_('COM_VIPQUOTES_QUOTE'),
            'b.name'      => JText::_('COM_VIPQUOTES_AUTHOR'),
            'd.title'     => JText::_('COM_VIPQUOTES_CATEGORY'),
            'c.name'      => JText::_('COM_VIPQUOTES_USER'),
            'a.hits'      => JText::_('COM_VIPQUOTES_HITS'),
            'a.created'   => JText::_('COM_VIPQUOTES_DATE'),
            'a.id'        => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        VipQuotesHelper::addSubmenu($this->getName());

        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("archived" => false, "trash" => false)), 'value', 'text', $this->state->get('filter.state'), true)
        );

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_CATEGORY'),
            'filter_category_id',
            JHtml::_('select.options', JHtml::_('category.options', 'com_vipquotes'), 'value', 'text', $this->state->get('filter.category_id'))
        );

        jimport("vipquotes.filter.options");
        $filters = new VipQuotes\Filter\Options(JFactory::getDbo());
        $authors = $filters->getAuthors();

        JHtmlSidebar::addFilter(
            JText::_('COM_VIPQUOTES_SELECT_AUTHOR'),
            'filter_author_id',
            JHtml::_('select.options', $authors, 'value', 'text', $this->state->get('filter.author_id'))
        );

        JHtmlSidebar::addFilter(
            JText::_('COM_VIPQUOTES_SELECT_USER'),
            'filter_user_id',
            JHtml::_('select.options', JHtml::_('user.userlist'), 'value', 'text', $this->state->get('filter.user_id'))
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
        JToolbarHelper::title(JText::_('COM_VIPQUOTES_QUOTES'));
        JToolbarHelper::addNew('quote.add');
        JToolbarHelper::editList('quote.edit');
        JToolbarHelper::divider();
        JToolbarHelper::publishList("quotes.publish");
        JToolbarHelper::unpublishList("quotes.unpublish");
        JToolbarHelper::divider();

        // Add custom buttons
        $bar = JToolBar::getInstance('toolbar');

        // Import
        $link = JRoute::_('index.php?option=com_vipquotes&view=import');
        $bar->appendButton('Link', 'upload', JText::_("COM_VIPQUOTES_IMPORT"), $link);

        // Export
        $link = JRoute::_('index.php?option=com_vipquotes&task=export.download&format=raw');
        $bar->appendButton('Link', 'download', JText::_("COM_VIPQUOTES_EXPORT"), $link);
        JToolbarHelper::divider();

        JToolbarHelper::deleteList(JText::_("COM_VIPQUOTES_DELETE_ITEMS_QUESTION"), "quotes.delete");
        JToolbarHelper::divider();
        JToolbarHelper::custom('quotes.backToDashboard', "dashboard", "", JText::_("COM_VIPQUOTES_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIPQUOTES_QUOTES'));

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('prism.ui.joomlaList');
    }
}
