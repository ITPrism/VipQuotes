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

defined('_JEXEC') or die;

jimport('joomla.application.categories');

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
    
    $mView	= (empty($menuItem->query['view']))       ? null : $menuItem->query['view'];
	$mCatid	= (empty($menuItem->query['catid']))      ? null : $menuItem->query['catid'];
	$mId	= (empty($menuItem->query['id']))         ? null : $menuItem->query['id'];
	$mOption = (empty($menuItem->query['option']))    ? null : $menuItem->query['option'];

	// If is set view and Itemid missing, we have to put the view to the segments
	if (isset($query['view'])) {
		$view = $query['view'];
		
		if (empty($query['Itemid']) OR ($mOption !== "com_vipquotes")) {
			$segments[] = $query['view'];
		}
        unset($query['view']);
	};
    
    // are we dealing with a category or author that is attached to a menu item?
	if (isset($view) AND ($mView == $view) AND (isset($query['id'])) AND ($mId == intval($query['id']))) {
		unset($query['view']);
		unset($query['catid']);
		unset($query['id']);
		return $segments;
	}
	
    // Views
	if(isset($view)) {
	    
    	switch($view) {
    	    
    	    case "category":
    	        if ($mId != intval($query['id']) || $mView != $view) {
    	            $catId = $query['id'];
    	            
    	            VipQuotesHelperRoute::prepareCategoriesSegments($catId, $segments, $mId);
    			    unset($query['id']);
		            unset($query['catid']);
    	        }
    	        
    	        break;
    	        
    	        
	        case "quote":
	            
    	        $catId      = $query['catid'];
    	        VipQuotesHelperRoute::prepareCategoriesSegments($catId, $segments, $mId);
    	        
    	        $id = $query['id'];
				$segments[] = $id;
				
				unset($query['id']);
	            unset($query['catid']);
    	        break;
	       
    	}
        
	}
    
	// Layout
    if (isset($query['layout'])) {
		if (!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {
				unset($query['layout']);
			}
		} else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	};
    
    return $segments;
}

/**
 * Method to parse Route
 * @param array $segments
 */
function VipQuotesParseRoute($segments){
    
    $vars = array();
    
    //Get the active menu item.
    $app        = JFactory::getApplication();
    $menu       = $app->getMenu();
    $item       = $menu->getActive();
    
    $db         = JFactory::getDBO();
    
    // Count route segments
    $count      = count($segments);
    
    // Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
	// the first segment is the view and the last segment is the id of the quote, category or author.
    if(!isset($item)) {
        $vars['view']   = $segments[0];
        $vars['catid']  = $segments[$count - 1];
        return $vars;
    } 
    
    // if there is only one segment, then it points to either an quote, author or a category
	// we test it first to see if it is a category.  If the id and alias match a category
	// then we assume it is a category.  If they don't we assume it is an quote
	if ($count == 1) {
	    
	    $view = $item->query["view"];
	    
		// we check to see if an alias is given.  If not, we assume it is an quote
		if (false === strpos($segments[0], ':')) {
			$vars['view'] = 'quote';
			$vars['id']   = (int)$segments[0];
			return $vars;
		}

		JLoader::register("VipQuotesHelperRoute", JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vipquotes".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."route.php");
		
		switch($view) {
		    
		    case "category":
		        
                
		        break;
		        
		    case "categories":
		        
        		list($id, $alias) = explode(':', $segments[0], 2);
        
        		// First we check if it is a category
        		$category = JCategories::getInstance('VipQuotes')->get($id);
        
        		if ($category && $category->alias == $alias) {
        			$vars['view'] = 'category';
        			$vars['id']   = $id;
        
        			return $vars;
        			
        		} 
        		
        		// Second we check if it is a quote
    		    $quote = VipQuotesHelperRoute::getQuote($id);
    			if ($quote) {
    				$vars['view']   = 'quote';
    				$vars['catid']  = (int)$quote->catid;
    				$vars['id']     = (int)$id;
    
    				return $vars;
    			}
        		
		        break;
		}
		
	}
	
	
    // if there was more than one segment, then we can determine where the URL points to
	// because the first segment will have the target category id prepended to it.  If the
	// last segment has a number prepended, it is an quote, otherwise, it is a category.
	$catId     = (int)$segments[0];
	$quoteId   = (int)$segments[$count - 1];

	if ($quoteId > 0) {
		$vars['view']   = 'quote';
		$vars['catid']  = $catId;
		$vars['id']     = $quoteId;
	} else {
		$vars['view']   = 'category';
		$vars['id']     = $catId;
	}

    return $vars;
}