<?php
/**
 * @package      ITPrism Components
 * @subpackage   Vip Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

class VipQuotesViewQuote extends JView {
    
    protected $state;
    protected $item;
    protected $form;
    
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
        
        JToolBarHelper::title($isNew ? JText::_('COM_VIPQUOTES_QUOTE_NEW')
		                             : JText::_('COM_VIPQUOTES_QUOTE_EDIT'), 'vip-quotes-new');
		                             
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
	    
	    $option = JRequest::getCmd("option");
	    
	    // Add behaviors
		//JHtml::_('behavior.modal', 'a.vip-modal');
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
        
		$this->document->setTitle(JText::_('COM_VIPQUOTES_QUOTE_NEW_ADMINISTRATION'));
        
		// Add scripts
		$this->document->addScript(JURI::root() . 'administrator/components/'.$option.'/views/'.$this->getName().'/submitbutton.js');
        
	}

}