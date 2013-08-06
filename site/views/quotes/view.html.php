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

jimport('joomla.application.categories');
jimport('joomla.application.component.view');

class VipQuotesViewQuotes extends JViewLegacy {
    
    protected $state = null;
    protected $items = null;
    protected $pagination = null;
    
    protected $option= null;
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }
    
    /**
     * Display the view
     *
     * @return  mixed   False on error, null otherwise.
     */
    public function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params     = $this->state->get("params");
        
        $this->categories = array();
        if($this->params->get("category_display_category", 1)) {
            $this->categories = VipQuotesHelper::getCategories();
        }
        
        $this->listView   = $this->params->get("quotes_list_view", "table");
        
        $this->displayCategory     = $this->params->get("quotes_display_category");
        $this->displayDate         = $this->params->get("quotes_display_date");
        $this->displayHits         = $this->params->get("quotes_display_hits");
        
        if($this->displayCategory OR $this->displayDate OR $this->displayHits) {
            $this->displayInfo = true;
        } else {
            $this->displayInfo = false;
        }
        
        // HTML Helpers
        JHtml::addIncludePath(VIPQUOTES_PATH_COMPONENT_SITE.'/helpers/html');
        
        $this->prepareFilters();
        $this->prepareDocument();
        
        // Prepare TMPL variable
        $tmpl = $app->input->get->get("tmpl", "");
        $this->tmplValue = "";
        if(strcmp("component", $tmpl) == 0) {
            $this->tmplValue = "&tmpl=component";
        }
        
        parent::display($tpl);
    }
    
    protected function prepareFilters() {
        
        // Filters
        $this->filterOrdering  = $this->params->get("quotes_display_filter_ordering", 0);
        $this->filterCategory  = $this->params->get("quotes_display_filter_category", 0);
        
        if($this->filterOrdering OR $this->filterCategory) {
            $this->displayFilters = true;
        } else {
            $this->displayFilters = false;
        }

        $this->numberOfFilters = 0;
        
        if($this->filterCategory) {
            $this->categoryOptions    = JHtml::_("category.options", "com_vipquotes", array("filter.published" => 1));
            $option = new stdClass();
            $option->value = 0;
            $option->text  = JText::_("COM_VIPQUOTES_SELECT_CATEGORY");
        
            array_unshift($this->categoryOptions, $option);
        
            // Increase the number of filters
            $this->numberOfFilters++;
        }
        
        if($this->filterOrdering) {
            $this->orderingOptions    =  array(
                array("value"=>'0', "text"=> JText::_("COM_VIPQUOTES_ORDER_OPTION_ORDERING")),
                array("value"=>'1', "text"=> JText::_("COM_VIPQUOTES_ORDER_OPTION_ADDED_ASC")),
                array("value"=>'2', "text"=> JText::_("COM_VIPQUOTES_ORDER_OPTION_ADDED_DESC")),
            );
            
            // Increase the number of filters
            $this->numberOfFilters++;
        }
        
        $this->spanClass = "span6";
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){

        $app        = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Prepare page heading
        $this->prepearePageHeading();
        
        // Prepare page heading
        $this->prepearePageTitle();
        
        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));
        
        // Meta keywords 
        $this->document->setDescription($this->params->get('menu-meta_keywords'));
        
        // Styles
        JHtml::_("vipquotes.bootstrap");
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Scripts
        JHtml::_('behavior.framework');
        
        if($this->displayFilters) {
		    $this->document->addScript('media/'.$this->option.'/js/site/'.strtolower($this->getName()).'.js');
        }
    }
    
    private function prepearePageHeading() {
        
        $app        = JFactory::getApplication();
        /** @var $app JSite **/
        
        $menus      = $app->getMenu();
        
        // Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu       = $menus->getActive();

		// Prepare page heading
		if ($menu) {
		    
		    $pageHeading = $this->params->get('page_heading', $this->params->get('page_title', $menu->title) );
		    $this->params->set('page_heading', $pageHeading);
		        
		} else {
		    $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE'));
		}
		
    }
    
    private function prepearePageTitle() {
        
        $app        = JFactory::getApplication();
        /** @var $app JSite **/
        
        $title      = "";
        
		// Prepare page title
        $title = $this->params->get('page_title');
        
        // Add title before or after Site Name
        if(!$title){
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
        $this->document->setTitle($title);
		
    }

}