<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport( 'joomla.application.component.controller' );

/**
 * Control Panel Controller
 *
 * @package		ITPrism Components
 * @subpackage	VipQuotes
  */
class VipQuotesController extends JControllerLegacy {
    
    protected $option;
    
	public function __construct($config = array())	{
		parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option", "com_vipquotes", "get");
	}
	
	public function display( ) {

		$document = JFactory::getDocument();
		/** @var $document JDocumentHtml **/
		
		// Add component style
        $document->addStyleSheet('../media/'.$this->option.'/css/admin/style.css');
        
        $viewName      = JFactory::getApplication()->input->getCmd('view', 'dashboard');
        JFactory::getApplication()->input->set("view", $viewName);
        
        parent::display();
        return $this;
	}

}