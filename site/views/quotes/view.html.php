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

class VipQuotesViewQuotes extends JView {
    
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
    function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Check for valid category
        $categoryId     = $app->input->getInt("catid", 0, "GET");
        $category       = null;
        
        // Checking for published category
        if(!empty($categoryId)){
            
			$options      = array("catid"=>$categoryId);
			$categories   = JCategories::getInstance('VipQuotes');
			$category     = $categories->get($categoryId);
			
            if(!$category->published){
                throw new Exception(JText::_("ITP_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
            }
            
        }
       
        // Get search phrase
        $this->query      = JString::trim( $app->input->getVar("q", "", "GET") );
        
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

        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $title      = "";
        $category   = $this->get("category");
        
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Preparet page heading
        if(!$this->params->get("page_heading")){
            if(!empty($category->title)) {
                $this->params->def('page_heading', $category->title);
            } else {
                
                $menus = $app->getMenu();
                
                // Because the application sets a default page title,
                // we need to get it from the menu item itself
                $menu  = $menus->getActive();
                if($menu) {
                    $this->params->def('page_heading', $menu->title);
                } else {
                    $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE'));
                }
            }
        }

        // Prepare page title
        if(!$category) { // Uncategorised
            // Get title from the page title option
            $title = $this->params->get("page_title");

            if(!$title) {
                $title = $app->getCfg('sitename');
            }
            
        } else{
            
            // Get title from the page title option
            $title = $this->params->get("page_title");
            
            if(!$title){
                
                $title = $category->title;
    
                if(!$title) {
                    $title = $app->getCfg('sitename');
                }
                
            }elseif($app->getCfg('sitename_pagetitles', 0)){ // Set site name if it is necessary ( the option 'sitename' = 1 )
                $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
                
            }
            
        }
        
        $this->document->setTitle($title);
        
        // Meta Description
        if(empty($category->metadesc)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($category->metadesc);
        }
        
        /*** Meta keywords ***/
        if(empty($category->metakey)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $category->metakey);
        }
        
        // Add category name into breadcrumbs 
        if($this->params->get('breadcrumb')){
            
            if(!empty($category->id) AND !empty($category->title)){
                $pathway    = $app->getPathway();
                $pathway->addItem($category->title);
            }
        }
        
        // Head styles
        $this->document->addStyleSheet('media/'.$this->option.'/css/bootstrap.css');
        $this->document->addStyleSheet('media/'.$this->option.'/css/style.css');
    }

}