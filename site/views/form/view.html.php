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

jimport('joomla.application.categories');
jimport('joomla.application.component.view');

class VipQuotesViewForm extends JView {
    
    protected $form;
	protected $item;
	protected $state;
	protected $return_page;
    
    public function display($tpl = null){
        
        $option     = JRequest::getCmd("option", "com_vipquotes", "GET");
        
        // Initialise variables.
		$app		= JFactory::getApplication();
//		$user		= JFactory::getUser();

		// Get model data.
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');
		
        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
        
        if (!empty($this->item)) {
			$this->form->bind($this->item);
		}
		
        // Create a shortcut to the parameters.
		$params	= $this->state->get("params");

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params	= $params;
//		$this->user		= $user;
		$this->version  = new VipQuotesVersion();

		if($this->item) {
		    $this->form->setFieldAttribute('catid', 'default',  $this->item->catid);
		}
		
		JHtml::_('behavior.keepalive');
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
		// Add Styles
		$this->document->addStyleSheet(JURI::root() . 'media/'.$option.'/css/style.css');
		// Add scripts
		$this->document->addScript(JURI::root() . 'components/'.$option.'/views/'.$this->getName().'/submitbutton.js');
		
		$this->prepareDocument();
		parent::display($tpl);
    }
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){

        $app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_VIPQUOTES_FORM_EDIT_ITEM'));
		}

		$title = $this->params->def('page_title', JText::_('COM_VIPQUOTES_FORM_EDIT_ITEM'));
		
		if ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
    }

}