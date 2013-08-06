<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipQuotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
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

class VipQuotesViewQuotes extends JView {
    
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

        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0 ) ? false : true;
        
        JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
        
        // Add submenu
        VipQuotesHelper::addSubmenu($this->getName());
        
        $this->addToolbar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_VIPQUOTES_QUOTES'), 'vip-quotes');
        JToolBarHelper::addNew('quote.add');
        JToolBarHelper::editList('quote.edit');
        JToolBarHelper::divider();
        JToolBarHelper::publishList("quotes.publish");
        JToolBarHelper::unpublishList("quotes.unpublish");
        JToolBarHelper::divider();
        
        // Add custom buttons
		$bar = JToolBar::getInstance('toolbar');
		
		// Import
		$link = JRoute::_('index.php?option=com_vipquotes&view=import');
		$bar->appendButton('Link', 'upload', JText::_("COM_VIPQUOTES_IMPORT"), $link);
		
		// Export
		$link = JRoute::_('index.php?option=com_vipquotes&task=export.download&format=raw');
		$bar->appendButton('Link', 'export', JText::_("COM_VIPQUOTES_EXPORT"), $link);
		JToolBarHelper::divider();
		
        JToolBarHelper::deleteList(JText::_("COM_VIPQUOTES_DELETE_ITEMS_QUESTION"), "quotes.delete");
        JToolBarHelper::divider();
        JToolBarHelper::custom('quotes.backToDashboard', "vip-dashboard-back", "", JText::_("COM_VIPQUOTES_DASHBOARD"), false);
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
		$this->document->setTitle(JText::_('COM_VIPQUOTES_QUOTES'));
	}
    
}