<?php
/**
 * @package      ITPrism Modules
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
defined( "_JEXEC" ) or die( "Restricted access" );

jimport('joomla.application.component.helper');

if (!JComponentHelper::isEnabled('com_vipquotes', true)) {
    echo JText::_('Module requires VipQuotes component.');
    return;
}

require_once(dirname(__FILE__).DS.'helper.php');

$qMarks     = $params->get('show_quotes', 1);
$number     = $params->get('number', 10);
$showAuthor = $params->get('show_author', 0);
$items  = modLastVipQuotesHelper::getItems($number);
require(JModuleHelper::getLayoutPath('mod_viplastquotes'));