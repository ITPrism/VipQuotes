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

class VipQuotesViewQuote extends JView {
    
    protected $state = null;
    protected $item  = null;
    
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
        $this->item       = $this->get('Item');
        $this->params     = $this->state->get("params");
        $this->userId     = JFactory::getUser()->get("id");
        
        if(!$this->item->published) {
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_QUOTE_DOES_NOT_EXIST"), 404);
        }
        
        // Checking for published category
		$category         = JCategories::getInstance('VipQuotes')->get($this->item->catid);
        if(!$category->published){
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
        }
        
        $this->category   = $category; 
        
        // Hit
        $model = $this->getModel();
        $model->hit($this->item->id);
        
        $this->version        = new VipQuotesVersion();
        
        // Prepare TMPL variable
        $tmpl = $app->input->get->get("tmpl", "");
        $this->tmplValue = "";
        if(strcmp("component", $tmpl) == 0) {
            $this->tmplValue = "&tmpl=component";
        }
        
        // HTML Helpers
        JHtml::addIncludePath(VIPQUOTES_PATH_COMPONENT_SITE.'/helpers/html');
        
        // Prepare document
        $this->prepareDocument();
        $this->prepareEvents();
        
        parent::display($tpl);
    }
    
    protected function prepareEvents() {
    
        $imagesFolder = $this->params->get("images_directory", "images/authors");
    
        // Prepare data used by triggers
        $this->item->link         = JRoute::_(VipQuotesHelperRoute::getQuoteRoute($this->item->id, $this->item->catid).$this->tmplValue);
        $this->item->title        = $this->pageHeading;
        $this->item->text         = $this->item->quote;
        $this->item->image_intro  = "";
    
        // Events
        JPluginHelper::importPlugin('content');
        $dispatcher	       = JDispatcher::getInstance();
        $this->item->event = new stdClass();
        $offset            = 0;
    
        $dispatcher->trigger('onContentPrepare', array ('com_vipquotes.quote', &$this->item, &$this->params, $offset));
    
        $results           = $dispatcher->trigger('onContentBeforeDisplay', array('com_vipquotes.quote', &$this->item, &$this->params, $offset));
        $this->item->event->onContentBeforeDisplay = trim(implode("\n", $results));
    
        $results           = $dispatcher->trigger('onContentAfterDisplay', array('com_vipquotes.quote', &$this->item, &$this->params, $offset));
        $this->item->event->onContentAfterDisplay  = trim(implode("\n", $results));
    
        // Replace the content of parameter 'qoute' with the parameter 'text'
        $this->item->quote = $this->item->text;
        unset($this->item->text);
    
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){

        $app        = JFactory::getApplication();
        /** @var $app JSite **/
        $menus      = $app->getMenu();
        
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
        
        // Add category name into breadcrumbs 
        if($this->params->get('category_breadcrumb', 0)){
            if(!empty($this->category->title)){
                $pathway      = $app->getPathway();
                
                $menu    = $app->getMenu()->getActive();
                $mId     = JArrayHelper::getValue($menu->query, "id");
                
                if(!empty($this->category->title) AND ($mId != $this->category->id) ){
                    $categoryLink = JRoute::_(VipQuotesHelperRoute::getCategoryRoute($this->category->id));
                    $pathway->addItem($this->category->title, $categoryLink);
                }
                
                $pathway->addItem(JText::_("COM_VIPQUOTES_QUOTE"));
            }
        }
        
        // Styles
        JHtml::_("vipquotes.bootstrap");
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
    }

    private function prepearePageHeading() {
        
		// Prepare page heading
		$this->pageHeading = JText::sprintf("COM_VIPQUOTES_QUOTE_HEADING_TITLE", JString::strtolower($this->category->title));
	    $this->params->set('page_heading', $this->pageHeading);
		
    }
    
    private function prepearePageTitle() {
        
        $app        = JFactory::getApplication();
        /** @var $app JSite **/
        
		// Prepare page title
        $title = JText::sprintf("COM_VIPQUOTES_QUOTE_HEADING_TITLE", $this->category->title);
        
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