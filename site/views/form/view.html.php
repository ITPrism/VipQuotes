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

class VipQuotesViewForm extends JViewLegacy {
    
    protected $form       = null;
    protected $state      = null;
    protected $item       = null;
    
    protected $option     = null;
    
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
        $this->form       = $this->get('Form');
        $this->params     = $this->state->get('params');
        
        $this->version    = new VipQuotesVersion();
        
        $this->prepareDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Prepare the document
     */
    protected function prepareDocument(){

        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $title      = "";
        
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Preparet page heading
        if(!$this->params->get("page_heading")){
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

        // Prepare page title
        // Get title from the page title option
        $title = $this->params->get("page_title");
        if(!$title){
            $title = $app->getCfg('sitename');
        }elseif($app->getCfg('sitename_pagetitles', 0)){ // Set site name if it is necessary ( the option 'sitename' = 1 )
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
            
        }
            
        $this->document->setTitle($title);
        
        // Meta Description
        if(empty($category->metadesc)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($category->metadesc);
        }
        
        // Meta keywords
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
        
        // Styles
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Add scripts
        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');
        
        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', '.js-vqform-categories');

		$this->document->addScript('media/'.$this->option.'/js/site/'.strtolower($this->getName()).'.js');
		
		// Language
		JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
    }

}