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

class VipQuotesViewQuote extends JViewLegacy {
    
    protected $state;
    protected $item;
    protected $form;
    
    protected $documentTitle;
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    /**
     * Display the view
     */
    public function display($tpl = null){
        
        $this->state= $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
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
        
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        
        $this->documentTitle = $isNew ? JText::_('COM_VIPQUOTES_QUOTE_NEW')
		                              : JText::_('COM_VIPQUOTES_QUOTE_EDIT');
        
		JToolbarHelper::title($this->documentTitle);
		                             
        JToolbarHelper::apply('quote.apply');
        JToolbarHelper::save2new('quote.save2new');
        JToolbarHelper::save('quote.save');
    
        if(!$isNew){
            JToolbarHelper::cancel('quote.cancel', 'JTOOLBAR_CANCEL');
        }else{
            JToolbarHelper::cancel('quote.cancel', 'JTOOLBAR_CLOSE');
        }
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle($this->documentTitle);
        
		// Add scripts
		JHtml::_('bootstrap.framework');
		JHtml::_('formbehavior.chosen', 'select');
		
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');
        
		$this->document->addScript('../media/'.$this->option.'/js/admin/'.strtolower($this->getName()).'.js');
		
	}

}