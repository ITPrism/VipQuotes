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

/**
 * The class conatins statics helper function
 *
 */
class VipQuotesHelper {
	
    public static $categoriesAliases = null;
    
    public static function getCategoriesAliases( $extension = "com_vipquotes" ) {
        
        if(is_null(self::$categoriesAliases)) {
            
            $db     = JFactory::getDbo();
            $query  = $db->getQuery(true);
            
            $query
            ->select("id, alias")
            ->from("#__categories")
            ->where("extension=" . $db->quote($extension));

            $db->setQuery($query);
            $categories = $db->loadAssocList("id", "alias");
            
            if(!$categories) {
                $categories = array();
            }
            
    		self::$categoriesAliases = $categories;
        }
        
        return self::$categoriesAliases;
			
    }
    
}