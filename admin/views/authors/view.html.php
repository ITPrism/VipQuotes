<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.application.categories');

class VipQuotesViewAuthors extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        $this->state       = $this->get('State');
        $this->items       = $this->get('Items');
        $this->pagination  = $this->get('Pagination');
        
        $this->params      = $this->state->get("params");
        
        // Get number of quotes for authors.
        jimport("vipquotes.authors");
        $this->authors     = new VipQuotesAthours(JFactory::getDbo());
        $this->authors->setItems($this->items);
        
        $this->authorsQuotesNumber = $this->authors->getQuotesNumber();
        
        // Prepare sorting data
        $this->prepareSorting();
        
        // Add submenu
        VipQuotesHelper::addSubmenu($this->getName());
        
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
	/**
     * Prepare sortable fields, sort values and filters. 
     */
    protected function prepareSorting() {
        
        // Prepare filters
        $this->listOrder  = $this->escape($this->state->get('list.ordering'));
        $this->listDirn   = $this->escape($this->state->get('list.direction'));
        $this->saveOrder  = (strcmp($this->listOrder, 'a.ordering') != 0 ) ? false : true;
        
        if ($this->saveOrder) {
        	$this->saveOrderingUrl = 'index.php?option='.$this->option.'&task='.$this->getName().'.saveOrderAjax&format=raw';
        	JHtml::_('sortablelist.sortable', $this->getName().'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }
        
        $this->sortFields = array(
			'a.ordering'  => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name'      => JText::_('COM_VIPQUOTES_NAME'),
			'a.hits'      => JText::_('COM_VIPQUOTES_HITS'),
			'a.id'        => JText::_('JGRID_HEADING_ID')
		);
        
    }
    
	/**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar() {
        
        JHtmlSidebar::setAction('index.php?option='.$this->option.'&view='.$this->getName());

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_state',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("archived" => false, "trash"=>false)), 'value', 'text', $this->state->get('filter.state'), true)
		);
		
		$this->sidebar = JHtmlSidebar::render();

    }
    
    /**
     * Add the page title and toolbar.
     * @since   1.6
     */
    protected function addToolbar(){
        
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_VIPQUOTES_AUTHORS'));
        JToolbarHelper::addNew('author.add');
        JToolbarHelper::editList('author.edit');
        JToolbarHelper::divider();
        JToolbarHelper::publishList("authors.publish");
        JToolbarHelper::unpublishList("authors.unpublish");
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_("COM_VIPQUOTES_DELETE_ITEMS_QUESTION"), "authors.delete");
        JToolbarHelper::divider();
        JToolbarHelper::custom('authors.backToDashboard', "dashboard", "", JText::_("COM_VIPQUOTES_DASHBOARD"), false);
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle(JText::_('COM_VIPQUOTES_AUTHORS'));
		
		// Scripts
		JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('itprism.ui.joomla_list');
        
	}
    
}