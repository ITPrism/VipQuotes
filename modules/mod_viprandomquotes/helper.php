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

class modVipQuotesHelper {
	
	public function getItem() {
		
		$db   =   JFactory::getDBO();
		
		$db->setQuery("SELECT  * 
          FROM    " . $db->nameQuote('#__vq_quotes') . "
          WHERE   published = 1 
          ORDER   BY RAND()
          LIMIT   1");
		
		return    $db->loadObject();
		
	}
	
}
?>