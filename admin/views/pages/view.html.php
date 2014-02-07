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

class VipQuotesViewPages extends JViewLegacy {
    
    protected $items;
    protected $pagination;
    protected $state;
    protected $params;
    
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null){
        
        $this->state         = $this->get('State');
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        
        $this->params        = $this->state->get('params');
        
        // HTML Helpers
        JHtml::addIncludePath(VIPQUOTES_PATH_COMPONENT_SITE.'/helpers/html');
        
        // Prepare sorting data
        $this->prepareSorting();
        
        // Add submenu
        VipQuotesHelper::addSubmenu($this->getName());
        
        // Prepare actions
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
        $listOrder        = $this->escape($this->state->get('list.ordering'));
        $listDirn         = $this->escape($this->state->get('list.direction'));
        $saveOrder        = (strcmp($listOrder, 'a.ordering') != 0 ) ? false : true;
        
        $this->listOrder  = $listOrder;
        $this->listDirn   = $listDirn;
        $this->saveOrder  = $saveOrder;
        
        if ($this->saveOrder) {
        	$this->saveOrderingUrl = 'index.php?option='.$this->option.'&task='.$this->getName().'.saveOrderAjax&format=raw';
        	JHtml::_('sortablelist.sortable', $this->getName().'List', 'adminForm', strtolower($listDirn), $this->saveOrderingUrl);
        }
        
        $this->sortFields = array(
			'a.published' => JText::_('JSTATUS'),
			'a.title'     => JText::_('COM_VIPQUOTES_TITLE'),
			'a.fans'      => JText::_('COM_VIPQUOTES_FANS'),
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
        JToolbarHelper::title(JText::_('COM_VIPQUOTES_FACEBOOK_PAGES'));
        
        // Facebook buttons
        if(!$this->params->get("fbpp_app_id") OR !$this->params->get("fbpp_app_secret")) {
            
            $app = JFactory::getApplication();
            /** @var $app JAdministrator **/
             
            // Add a message to the message queue
            $app->enqueueMessage(JText::_('COM_VIPQUOTES_ERROR_FACEBOOK_MISSING_SETTINGS'), 'Notice');

        } else {
        
            $facebook = new Facebook(array(
                'appId'      => $this->params->get("fbpp_app_id"),
                'secret'     => $this->params->get("fbpp_app_secret"),
                'fileUpload' => false
            ));
            
            $facebookUserId = $facebook->getUser();
            
            JToolbarHelper::divider();
            if(!$facebookUserId) {
                JToolbarHelper::custom('pages.connect', "globe", "", JText::_("COM_VIPQUOTES_CONNECT"), false);                
            } else {
                JToolbarHelper::custom('pages.update', "refresh", "", JText::_("COM_VIPQUOTES_UPDATE_ALL"), false);
            }
            
        }
        
        // Back button
        JToolbarHelper::divider();
        JToolbarHelper::custom('pages.backToDashboard', "dashboard", "", JText::_("COM_VIPQUOTES_DASHBOARD"), false);
    }
    
	/**
	 * Method to set up the document properties
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle(JText::_('COM_VIPQUOTES_FACEBOOK_PAGES_ADMINISTRATION'));
		
		// Scripts
		JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.multiselect');
        JHtml::_('formbehavior.chosen', 'select');
        JHtml::_('itprism.ui.joomla_list');
	}

}