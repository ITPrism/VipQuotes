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
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $this->option = $app->input->getCmd("option", "com_vipquotes", "GET");
    }
    
    public function display($tpl = null){
        
        $categories_       = array();
        
        $this->state       = $this->get('State');
        $this->items       = $this->get('Items');
        $this->pagination  = $this->get('Pagination');
        
        $categories        = $this->get("Categories");
        
        foreach( $categories as $category ) {
            $categories_[$category->id] = $category->title;
        }
        
        $this->categories  = $categories_;
        
        // Check for errors.
        if(count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        
        // Prepare filters
        $listOrder  = $this->escape($this->state->get('list.ordering'));
        $listDirn   = $this->escape($this->state->get('list.direction'));
        $saveOrder  = (strcmp($listOrder, 'a.ordering') != 0 ) ? false : true;
        
        $this->assign("listOrder", $listOrder);
        $this->assign("listDirn",  $listDirn);
        $this->assign("saveOrder", $saveOrder);
        
        // Prepare actions
        $this->addToolbar();
        
        // Prepare document
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
        JToolBarHelper::deleteList(JText::_("COM_VIPQUOTES_DELETE_ITEMS_QUESTION"), "quotes.delete");
        JToolBarHelper::divider();
        JToolBarHelper::custom('quotes.backToControlPanel', "vip-properties-back", "", JText::_("COM_VIPQUOTES_BACK"), false);
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_VIPQUOTES_QUOTES_LIST') . " | " . JText::_('COM_VIPQUOTES'));
		
		// Add styles
		$this->document->addStyleSheet('../media/'.$this->option.'/css/style.css');
		
	}
    
}