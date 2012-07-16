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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.controller' );

/**
 * Control Panel Controller
 *
 * @package		ITPrism Components
 * @subpackage	VipQuotes
  */
class VipQuotesController extends JController {
    
	public function display( ) {

		$app = JFactory::getApplication();
        /** @var $app JAdministrator **/
		
        $viewName      = $app->input->getCmd('view', 'cpanel');
        JRequest::setVar("view", $viewName);
        
        parent::display();
        return $this;
	}

}