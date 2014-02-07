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

jimport('joomla.application.component.view');

class VipQuotesViewDashboard extends JViewLegacy {
    
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
        
        // Get latest started.
        jimport("vipquotes.statistics.quotes.latest");
        $this->latestQuotes = new VipQuotesStatisticsQuotesLatest(JFactory::getDbo());
        $this->latestQuotes->load(5);
        
        // Get popular quotes.
        jimport("vipquotes.statistics.quotes.popular");
        $this->popularQuotes = new VipQuotesStatisticsQuotesPopular(JFactory::getDbo());
        $this->popularQuotes->load(5);
        
        // Get popular authors.
        jimport("vipquotes.statistics.authors.popular");
        $this->popularAuthors = new VipQuotesStatisticsAuthorsPopular(JFactory::getDbo());
        $this->popularAuthors->load(5);
        
        // Get basic data
        jimport("vipquotes.statistics.basic");
        $basic = new VipQuotesStatisticsBasic(JFactory::getDbo());
        $this->totalQuotes  = $basic->getTotalQuotes();
        $this->totalAuthors = $basic->getTotalAuthors();
        
        // Add submenu
        VipQuotesHelper::addSubmenu($this->getName());
        
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar(){
        JToolbarHelper::title(JText::_("COM_VIPQUOTES_DASHBOARD"));
        
        JToolbarHelper::preferences('com_vipquotes');
        JToolbarHelper::divider();
        
        // Help button
        $bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_VIPQUOTES_HELP_URL'));
    }

	/**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar() {
		$this->sidebar = JHtmlSidebar::render();
    }
    
	/**
	 * Method to set up the document properties
	 * @return void
	 */
	protected function setDocument() {
	    $this->document->setTitle(JText::_('COM_VIPQUOTES_DASHBOARD_ADMINISTRATION'));
	}
	
}