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

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

JLoader::register("VipQuotesHelperRoute", JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vipquotes".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."route.php");

$limit = $params->get('number', 10);

// Get random item from database
$db    =   JFactory::getDBO();
$query =   $db->getQuery(true);
$query
    ->select("a.quote")
    ->from("#__vq_quotes AS a");

// Filter by category id
$categoryId = $params->get("category", 0);
if(!empty($categoryId)) {
    $query->where("a.catid = ".(int)$categoryId);
}

$query
    ->where("a.published = 1")
    ->order("a.created DESC");

$db->setQuery($query, 0, $limit);
$items = $db->loadObjectList();

if(!empty($items)) {
    
    foreach($items as $key => $item) {
        
        $item->quote = strip_tags($item->quote);
        
        if ( $params->get('display_quotes', 1) ) {
        	$item->quote = '"' . $item->quote . '"';
        }
        
        $item->quote  = htmlentities($item->quote, ENT_QUOTES, "UTF-8");
        
        $items[$key] = $item;
    }
}

require JModuleHelper::getLayoutPath('mod_viplastquotes', $params->get('layout', 'default'));