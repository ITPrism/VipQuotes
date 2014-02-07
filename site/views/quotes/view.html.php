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
        
        $this->userId     = JFactory::getUser()->get("id");
        
        $this->categories = array();
        if($this->params->get("category_display_category", 1)) {
            $this->categories = VipQuotesHelper::getCategories();
        }
        
        $this->listView   = $this->params->get("quotes_list_view", "table");
        
        $this->displayAuthor       = $this->params->get("quotes_display_author");
        $this->displayPublisher    = $this->params->get("quotes_display_publisher");
        $this->displayInfo         = $this->params->get("quotes_display_info");
        
        $this->version    = new VipQuotesVersion();
        
        $this->prepareFilters();
        $this->prepareDocument();
        
        if(!empty($this->displayPublisher)) {
            $this->prepareIntegration($this->items, $this->params);
        }
        
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
        $this->filterAuthor    = $this->params->get("quotes_display_filter_author", 0);
        $this->filterCategory  = $this->params->get("quotes_display_filter_category", 0);
        $this->filterUser      = $this->params->get("quotes_display_filter_user", 0);
        $this->filterOrdering  = $this->params->get("quotes_display_filter_ordering", 0);
        
        if($this->filterAuthor OR $this->filterUser OR $this->filterOrdering OR $this->filterCategory) {
            $this->displayFilters = true;
        } else {
            $this->displayFilters = false;
        }

        $this->numberOfFilters = 0;
        
        if($this->filterAuthor) {
            jimport("vipquotes.filter.options");
            $filters        = VipQuotesFilterOptions::getInstance(JFactory::getDbo());

            $this->authors  = $filters->getAuthors(array("state" => VipQuotesConstants::PUBLISHED));
            
            $option = array(
                "value" => 0,
                "text"  => JText::_("COM_VIPQUOTES_SELECT_AUTHOR")
            );
            
            array_unshift($this->authors, $option);
            
            // Increase the number of filters
            $this->numberOfFilters++;
        }
        
        if($this->filterUser) {
            $this->users    = JHtml::_('user.userlist');
            $option = array(
                "value" => 0,
                "text"  => JText::_("COM_VIPQUOTES_SELECT_USER")
            );
            
            array_unshift($this->users, $option);
            
            // Increase the number of filters
            $this->numberOfFilters++;
        }
        
        
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
            jimport("vipquotes.filter.options");
            $filters        = VipQuotesFilterOptions::getInstance(JFactory::getDbo());
            
            $this->orderingOptions    =  $filters->getQuotesOrdering();
            
            // Increase the number of filters
            $this->numberOfFilters++;
        }
        
        $this->spanClass = "span4";
        if($this->numberOfFilters == 4) {
            $this->spanClass = "span3";
        }
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
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Scripts
        JHtml::_('bootstrap.framework');
        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select.js-vqcom-filter');
        
        if($this->displayFilters) {
            /* @todo use chosen and replace Mootools Scripts with jQuery */
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

    /**
     * Prepare social profiles.
     *
     * @param array     $items
     * @param JRegistry $params
     *
     * @todo Move it to a trait when traits become mass.
     */
    protected function prepareIntegration($items, $params) {
    
        $this->socialProfiles   = null;
        
        // Get users IDs
        $usersIds = array();
        foreach($items as $item) {
            $usersIds[] = $item->user_id;
        }
    
        // Get a social platform for integration
        $socialPlatform        = $params->get("integration_social_platform");
        
        // If there is now users, do not continue.
        if(!$usersIds OR !$socialPlatform) {
            return;
        }
    
        // Create an object that contains social profiles.
        if(!empty($socialPlatform)) {
            jimport("itprism.integrate.profiles");
            try {
                $this->socialProfiles   =  ITPrismIntegrateProfiles::factory($socialPlatform, $usersIds);
            } catch (Exception $e) {
                
                $app      = JFactory::getApplication();
                /** @var $app JSite **/
                
                $app->enqueueMessage(JText::_("COM_VIPQUOTES_ERROR_SOCIAL_INTEGRATION_PROBLEM"), "error");
            }
        }
    }
}