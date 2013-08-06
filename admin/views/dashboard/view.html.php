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

class VipQuotesViewDashboard extends JView {
    
    protected $option = "";
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        jimport("vipquotes.version");
        $this->version = new VipQuotesVersion();
        
        // Load ITPrism library version
        jimport("itprism.version");
        if(!class_exists("ITPrismVersion")) {
            $this->itprismVersion = JText::_("COM_VIPQUOTES_ITPRISM_LIBRARY_DOWNLOAD");
        } else {
            $itprismVersion = new ITPrismVersion();
            $this->itprismVersion = $itprismVersion->getShortVersion();
        }
        
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
        JToolBarHelper::title(JText::_("COM_VIPQUOTES_DASHBOARD"), 'vip-dashboard');
        
        JToolBarHelper::preferences('com_vipquotes');
        JToolBarHelper::divider();
        
        // Help button
        $bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_VIPQUOTES_HELP_URL'));
		
    }

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() {
	    
	    $this->document->setTitle(JText::_('COM_VIPQUOTES_DASHBOARD_ADMINISTRATION'));
	    
	    // Styles
	    $this->document->addStyleSheet('../media/'.$this->option.'/css/admin/bootstrap.min.css');
	    
	    // Scripts
	    JHtml::_('behavior.modal', 'a.modal');
	    
	}
	
}