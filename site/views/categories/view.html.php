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

jimport('joomla.application.component.view');

class VipQuotesViewCategories extends JView {
    
    protected $state = null;
    protected $items = null;
    protected $pagination = null;
    
    public function display($tpl = null) {
        
        $option     = JRequest::getCmd("option", "com_vipquotes", "GET");
        
        $app        = JFactory::getApplication();
        // Initialise variables
        $items      = $this->get('Items');
        $params     = $app->getParams();
        
        //Escape strings for HTML output
        $this->assign("pageclass_sfx", htmlspecialchars($params->get('pageclass_sfx')) );
        
        $this->assignRef('params', $params);
        $this->assignRef('items', $items);
        
        $this->assignRef( "version",    new VipQuotesVersion() );
        
        // Add template style
        $this->document->addStyleSheet( JURI::root() . 'media/'.$option.'/css/style.css');
                
        $this->prepareDocument();
                
        parent::display($tpl);
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if($menu){
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }else{
            $this->params->def('page_heading', JText::_('COM_VIPQUOTES_CATEGORIES_DEFAULT_PAGE_TITLE'));
        }
        
        /*** Set page title ***/
        $title = $this->params->get('page_title', '');
        if(empty($title)){
            $title = $app->getCfg('sitename');
        }elseif($app->getCfg('sitename_pagetitles', 0)){
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        $this->document->setTitle($title);
        
        /*** Meta Description ***/
        if($this->params->get('menu-meta_description')){
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }
        
        /*** Meta keywords ***/
        if($this->params->get('menu-meta_keywords')){
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }
        
    }
    
}