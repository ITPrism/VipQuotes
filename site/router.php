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

defined('_JEXEC') or die();

/**
 * Method to build Route
 * @param array $query
 */
function VipQuotesBuildRoute(&$query){
    
    $segments = array();
    
    // get a menu item based on Itemid or currently active
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();
    
    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if(empty($query['Itemid'])){
        $menuItem = $menu->getActive();
    }else{
        $menuItem = $menu->getItem($query['Itemid']);
    }
    
    if(isset($query['view'])){
        $view = $query['view'];
        unset($query['view']);
        
        if(empty($query['Itemid'])){
            $segments[] = $view;
        }
    }
    
    if(isset($query['catid'])){
        
        $categoryId = $query['catid'];
        unset($query['catid']);
        
        static $categories = null;
        
        if(is_null($categories)) {
            $categories = VipQuotesHelper::getCategoriesAliases();
        }
        
        if(array_key_exists($categoryId, $categories)){
            $segments[] = $categories[$categoryId];
        }
    
    }
    
    // Check for existing layout
    if(isset($query['layout'])){
        unset($query['layout']);
    }
    
    if(isset($query['format'])){
        unset($query['format']);
    }
    
    return $segments;
}

/**
 * Method to parse Route
 * @param array $segments
 */
function VipQuotesParseRoute($segments){
    
    $query = array();
    
    //Get the active menu item.
    $app            = JFactory::getApplication();
    $menu           = $app->getMenu();
    $menuItem       = $menu->getActive();
    
    $count          = count($segments);
    $categoryIndex  = $count-1;
    $categoryAlias  = null;
    
    if(!$menuItem) {
        $query['view']   = $segments[0];
        return $query;
    }
    
    if(isset($menuItem->query['view'])) {
        $view = $menuItem->query['view'];
    } else {
        $view = "quotes";
    }
        
    // Get variables from the menu item
    switch($view) {
        
        case "categories":
            
            $query['view']   = "quotes";
            
            // Get the category id from the menu item
            if(isset($menuItem->query['catid'])) {
                $query['catid']  = intval($menuItem->query['catid']);
            } else if(isset($segments[$categoryIndex])) {
                
                // Get category id by alias
                $categoryAlias = $segments[$categoryIndex];

                static $categories = null;
                if(is_null($categories)) {
                    require_once JPATH_ADMINISTRATOR.DS."components".DS."com_vipquotes".DS."helpers".DS."helper.php";
                    $categories = VipQuotesHelper::getCategoriesAliases();
                }
                
                $categoryId = array_search($categoryAlias, $categories);
                
                if(!empty($categoryId)){
                    $query['catid'] = intval($categoryId);
                }
                
            }
        
            break;
            
        default: // quotes
            
            $query['view']   = "quotes";
            
            // Get the category id from the menu item
            if(isset($menuItem->query['catid'])) {
                $query['catid']  = intval($menuItem->query['catid']);
            }
            
            break;
    }
    
    return $query;
}