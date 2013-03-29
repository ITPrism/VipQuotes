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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class VipQuotesViewImport extends JView {
    
    protected $state;
    protected $form;
    
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    /**
     * Display the view
     */
    public function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $this->state = $this->get('State');
        $this->form  = $this->get('Form');
        
        // Add submenu
        VipQuotesHelper::addSubmenu("quotes");
        
        // Prepare actions
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
        JToolBarHelper::title(JText::_('COM_VIPQUOTES_IMPORT_MANAGER'), 'vip-import');
        
        // Upload
		JToolBarHelper::custom("import.quotes", "upload", "", JText::_("COM_VIPQUOTES_UPLOAD"), false);
		
        JToolBarHelper::divider();
        JToolBarHelper::cancel('import.cancel', 'JTOOLBAR_CANCEL');
        
    }
    
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
		$this->document->setTitle(JText::_('COM_VIPQUOTES_IMPORT_MANAGER'));
		
		// Add scripts
		JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
        
		$this->document->addScript('../media/'.$this->option.'/js/admin/'.JString::strtolower($this->getName()).'.js');
	}
	
}
