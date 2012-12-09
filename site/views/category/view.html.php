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

class VipQuotesViewCategory extends JView {
    
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
        
        // Check for valid category
        $categoryId     = $app->input->getInt("id", 0);
        $category       = null;
        
        // Checking for published category
        if(!empty($categoryId)){
            
			$category   = JCategories::getInstance('VipQuotes')->get($categoryId);
			
            if(!$category->published){
                throw new Exception(JText::_("ITP_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
            }
            
        }
       
        // Get search phrase
        $this->query      = JString::trim( $app->input->get("q", "") );
        
        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params     = $this->state->get("params");
        
        $this->category   = $category;
        
        $this->version    = new VipQuotesVersion();
        
        $this->prepareDocument();
        
        parent::display($tpl);
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
        if(empty($this->category->metadesc)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($this->category->metadesc);
        }
        
        // Meta keywords 
        if(empty($this->category->metakey)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $this->category->metakey);
        }
        
        // Add item name into breadcrumbs 
        if($this->params->get('categories_breadcrumb', 0)){
            
            $menu    = $app->getMenu()->getActive();
            $mId     = JArrayHelper::getValue($menu->query, "id");
            
            if(!empty($this->category->title) AND ($mId != $this->category->id) ){
                $pathway    = $app->getPathway();
                $pathway->addItem($this->category->title);
            }
        }
        
        // Head styles
        $this->document->addStyleSheet('media/'.$this->option.'/css/bootstrap.min.css');
        $this->document->addStyleSheet('media/'.$this->option.'/css/style.css');
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
		    
		    $seo = $this->params->get("seo_cat_as_page_heading");
		    
		    switch($seo) {
		        
		        case "1": // Before page heading
		            $pageHeading = $this->category->title . " | " . $this->params->get('page_heading');
		            break;
		            
	            case "2": // After page heading
		            $pageHeading = $this->params->get('page_heading') . " | " . $this->category->title;
		            break;
		            
		        case "3": // Only category name
		            $pageHeading = $this->category->title;
		            $this->params->set('page_heading', $pageHeading);
		            break;
		        
		        default: // NONE
		            $pageHeading = $this->params->get('page_heading', $this->params->get('page_title', $menu->title) );
	                break;
		    }
		    
		    $this->params->set('page_heading', $pageHeading);
		        
		} else {
		    
		    if(!empty($this->category->title)) {
                $this->params->def('page_heading', $this->category->title);
            } else {
                $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE'));
            }
		}
		
    }
    
    private function prepearePageTitle() {
        
        $app        = JFactory::getApplication();
        /** @var $app JSite **/
        
        $title      = "";
        
		// Prepare page title
        if(!$this->params->get("page_title")) {
            $title = $this->category->title;
        } else {
            
            $seo = $this->params->get("seo_cat_to_title");
            
            switch($seo) {
		        
		        case "1": // Before page title
		            $title = $this->category->title . " | " . $this->params->get("page_title");
		            break;
		            
	            case "2": // After page title
		            $title = $this->params->get('page_title') . " | " . $this->category->title;
		            break;
		            
		        case "3": // Only category name
		            $title = $this->category->title;
		            break;
		        
		        default: // NONE
		            $title = $this->params->get('page_title');
	                break;
		    }
            
        }
        
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