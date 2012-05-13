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
defined('_JEXEC') or die();

jimport( 'joomla.application.categories' );
jimport('joomla.application.component.view');

class VipQuotesViewQuotes extends JView {
    
    protected $state = null;
    protected $items = null;
    protected $pagination = null;
    
    /**
     * Display the view
     *
     * @return  mixed   False on error, null otherwise.
     */
    function display($tpl = null){
        
        // Check for valid category
        $categoryId = JRequest::getInt("catid", 0, "GET");
        $option     = JRequest::getCmd("option", "com_vipquotes", "GET");
        $category   = null;
        
        // Checking for published category
        if(!empty($categoryId)){
            
			$options      = array("catid"=>$categoryId);
			$categories   = JCategories::getInstance('VipQuotes');
			$category     = $categories->get($categoryId);
			 
            if(empty($category->published)){
                throw new Exception(JText::_("ITP_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
            }
        }
       
        // Initialise variables
        $state      = $this->get('State');
        $items      = $this->get('Items');
        $pagination = $this->get('Pagination');
        
        $params     = $state->get("params");
        
        //Escape strings for HTML output
        $this->assign('pageclass_sfx', htmlspecialchars($params->get('pageclass_sfx')));
        
        $this->assignRef('params',       $params);
        $this->assignRef('items',        $items);
        $this->assignRef('pagination',   $pagination);
        $this->assignRef('category',     $category);
        $this->assignRef( "version",     new VipQuotesVersion() );

        $this->document->addStyleSheet(JURI::root() . 'media/'.$option.'/css/bootstrap.css');
        $this->prepareDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){

        $app        = JFactory::getApplication();
        $title      = "";
        $category   = $this->get("category");
        
        $menus      = $app->getMenu();
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu       = $menus->getActive();
        
        /*** Set page heading ***/
        if(!$this->params->get("page_heading")){
            if(!empty($category->title)) {
                $this->params->def('page_heading', $category->title);
            } else {
                if($menu) {
                    $this->params->def('page_heading', $menu->title);
                } else {
                    $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE'));
                }
            }
        }

        /*** Set page title ***/
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
        
        /*** Meta Description ***/
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
        
        /*** Add the category name into breadcrumbs ***/
        if($this->params->get('catToBreadcrumb')){
            
            if(!empty($category->id) AND !empty($category->title)){
                $pathway    = $app->getPathway();
                $pathway->addItem($category->title);
            }
        }
    }

}