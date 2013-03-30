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
defined( "_JEXEC" ) or die;

$limit     = $params->get('number', 10);

// Get random item from database
$db    =   JFactory::getDBO();
$query =   $db->getQuery(true);
$query
    ->select("a.quote")
    ->from("#__vq_quotes AS a")
    ->where("a.published = 1")
    ->order("a.created");

$db->setQuery($query, 0, $limit);
$items = $db->loadObjectList();

if(!empty($items)) {
    
    foreach($items as &$item) {
        
        $item->quote = strip_tags($item->quote);
        
        if ( $params->get('display_quotes', 1) ) {
        	$item->quote = '"' . $item->quote . '"';
        }
        
        $item->quote  = htmlentities($item->quote, ENT_QUOTES, "UTF-8");
    }
}

require JModuleHelper::getLayoutPath('mod_viplastquotes', $params->get('layout', 'default'));