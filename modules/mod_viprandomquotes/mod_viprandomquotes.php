<?php
/**
 * @package      ITPrism Modules
 * @subpackage   Vip Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor.iliev@itprism.co.uk>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( "_JEXEC" ) or die( "Restricted access" );

jimport('joomla.application.component.helper');

if (!JComponentHelper::isEnabled('com_vipquotes', true)) {
	
    JError::raiseError(404, JText::_('Module requires the Vip Quotes component.'));
    
}

require_once(dirname(__FILE__).DS.'helper.php');
$item = modVipQuotesHelper::getItem();

$quatationMarks = $params->get('show_quotes', 1);
require(JModuleHelper::getLayoutPath('mod_viprandomquotes'));