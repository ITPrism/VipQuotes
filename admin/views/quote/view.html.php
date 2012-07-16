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

class VipQuotesViewQuote extends JView {
    
    protected $documentTitle;
    protected $option;
    
    protected $state;
    protected $item;
    protected $form;
    
    public function __construct($config) {
        
        parent::__construct($config);
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $this->option = $app->input->getCmd("option", "com_vipquotes", "GET");
    }
    
    /**
     * Display the view
     */
    public function display($tpl = null){
        
        $this->state= $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
        // Check for errors.
        if(count($errors = $this->get('Errors'))){
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        
        // Prepare actions, behaviors, scritps and document
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
        
        JRequest::setVar('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        
        $this->documentTitle = $isNew ? JText::_('COM_VIPQUOTES_QUOTE_NEW')
		                             : JText::_('COM_VIPQUOTES_QUOTE_EDIT');
        
        JToolBarHelper::title($this->documentTitle, 'vip-quotes-new');
		                             
        JToolBarHelper::apply('quote.apply');
        JToolBarHelper::save2new('quote.save2new');
        JToolBarHelper::save('quote.save');
    
        if(!$isNew){
            JToolBarHelper::cancel('quote.cancel', 'JTOOLBAR_CANCEL');
        }else{
            JToolBarHelper::cancel('quote.cancel', 'JTOOLBAR_CLOSE');
        }
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
	    // Add behaviors
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
        
		$this->document->setTitle($this->documentTitle . " | ". JText::_("COM_VIPQUOTES"));
        
		// Add scripts
		$this->document->addScript('../media/'.$this->option.'/js/admin/quote.js');
		
		// Add styles
		$this->document->addStyleSheet('../media/'.$this->option.'/css/style.css');
        
	}

}