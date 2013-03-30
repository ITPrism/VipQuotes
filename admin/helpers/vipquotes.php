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
	
    public static $extension         = 'com_vipquotes';
    
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
		
	}
    
}