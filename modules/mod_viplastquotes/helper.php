<?php
/**
 * @package      ITPrism Modules
 * @subpackage   VipQuotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor.iliev@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

class modLastVipQuotesHelper {
	
	public function getItems($number = 10) {
		
		$db   =   JFactory::getDBO();
		
		$db->setQuery("
		  SELECT  
		      * 
          FROM    
            " . $db->nameQuote('#__vq_quotes') . "
          WHERE   
            published = 1 
          ORDER BY 
            `id` DESC", 0, $number);
		
		return $db->loadObjectList();
		
	}
	
}
?>