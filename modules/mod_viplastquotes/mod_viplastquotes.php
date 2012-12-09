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

JLoader::register("VipQuotesHelperRoute", JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vipquotes".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."route.php");

$limit     = $params->get('number', 10);

// Get random item from database
$db    =   JFactory::getDBO();
$query =   $db->getQuery(true);
$query
    ->select("a.quote, b.name AS author")
    ->select($query->concatenate(array("b.id", "b.alias"),":") . " AS author_slug")
    ->from("#__vq_quotes AS a")
    ->innerJoin("#__vq_authors AS b ON a.author_id = b.id")
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
        $item->author = htmlentities($item->author, ENT_QUOTES, "UTF-8");
    }
}

require JModuleHelper::getLayoutPath('mod_viplastquotes', $params->get('layout', 'default'));