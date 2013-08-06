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
defined('_JEXEC') or die;

/**
 * The class conatins statics helper function
 *
 */
class VipQuotesHelper {
	
    public static $extension              = 'com_vipquotes';
    public static $categories             = null;
    public static $categoriesQuotesNumber = null;
    
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'dashboard') {
	    
	    JSubMenuHelper::addEntry(
			JText::_('COM_VIPQUOTES_DASHBOARD'),
			'index.php?option='.self::$extension.'&view=dashboard',
			$vName == 'dashboard'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_VIPQUOTES_CATEGORIES'),
			'index.php?option=com_categories&extension='.self::$extension,
			$vName == 'categories'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_VIPQUOTES_QUOTES'),
			'index.php?option='.self::$extension.'&amp;view=quotes',
			$vName == 'quotes'
		);
		
		JSubMenuHelper::addEntry(
    		JText::_('COM_VIPQUOTES_PLUGINS'),
    		'index.php?option=com_plugins&view=plugins&filter_search='.rawurlencode("vip quotes"),
    		$vName == 'plugins'
        );
	}
	
	public static function getCategories($index = "id") {
	
	    if( is_null(self::$categories) ) {
	
	        $db     = JFactory::getDbo();
	        /** @var $db JDatabaseMySQLi **/
	
	        // Create a new query object.
	        $query  = $db->getQuery(true);
	
	        // Select the required fields from the table.
	        $query
	        ->select('a.id, a.title, a.alias')
	        ->from($db->quoteName("#__categories") . ' AS a')
	        ->where('a.extension = "com_vipquotes"')
	        ->where('a.published = 1');
	
	        $db->setQuery($query);
	
	        self::$categories  = $db->loadAssocList($index);
	
	    }
	
	    return self::$categories;
	}
	
	public static function getSubCategories($parentId) {
	
	    $db     = JFactory::getDbo();
	    /** @var $db JDatabaseMySQLi **/
	
	    // Create a new query object.
	    $query  = $db->getQuery(true);
	
	    // Select the required fields from the table.
	    $query
	    ->select('a.id, a.title, a.params, ' . $query->concatenate(array("a.id", "a.alias"), ":" ) . " AS slug")
	    ->from($db->quoteName("#__categories") . ' AS a')
	    ->where('a.extension = "com_vipquotes"')
	    ->where('a.published = 1')
	    ->where('a.parent_id = '.(int)$parentId);
	
	    $db->setQuery($query);
	
	    $result = $db->loadObjectList("id");
	    if(!empty($result)) {
	
	        foreach($result as $key => $item) {
	            $item->params = json_decode($item->params, true);
	            $result[$key] = $item;
	        }
	
	    } else {
	        $result = array();
	    }
	
	
	    return $result;
	}
	
	/**
	 * Count and return quotes number in categories.
	 *
	 * @return array
	 */
	public static function getCategoryQuotesNumber($state = null) {
	
	    if (is_null(self::$categoriesQuotesNumber)) {
	
	        $db     = JFactory::getDbo();
	        /** @var $db JDatabaseMySQLi **/
	
	        $query  = $db->getQuery(true);
	
	        $query
	        ->select("a.catid, COUNT(*) AS number")
	        ->from( $db->quoteName("#__vq_quotes") . ' AS a' )
	        ->group("a.catid");
	
	        if(is_null($state)) {// All
	            $query->where("a.published IN (0, 1)");
	        } else if($state == 0) { // Unpublished
	            $query->where("a.published = 0");
	        } else if($state == 1) { // Published
	            $query->where("a.published = 1");
	        }
	
	        $db->setQuery($query);
	        $results = $db->loadAssocList("catid", "number");
	
	        if(!$results) {
	            $results = array();
	        }
	
	        self::$categoriesQuotesNumber = $results;
	    }
	
	    return self::$categoriesQuotesNumber;
	}
    
}