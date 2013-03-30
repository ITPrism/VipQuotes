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

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Component Route Helper that help to find a menu item.
 * IMPORTANT: It help us to find right MENU ITEM.
 * 
 * Use router ...BuildRoute to build a link
 *
 * @static
 * @package		ITPrism Components
 * @subpackage	VipQuotes
 * @since 1.5
 */
abstract class VipQuotesHelperRoute {
    
	protected static $lookup;

	/**
	 * This method route quote in the view "category".
	 * 
	 * @param	int	The route of the quote
	 * @param   int Category Id
	 */
	public static function getQuoteRoute($id, $catid) {
	    
	    /**
	     * 
	     * # category
	     * We will check for view category first. If find a menu item with view "category" and "id" eqallity of the key, 
	     * we will get that menu item ( Itemid ). 
	     * 
	     * # categories view
	     * If miss a menu item with view "category" we continue with searchin but now for view "categories".
	     * It is assumed view "categories" will be in the first level of the menu.
	     * The view "categories" won't contain category ID so it has to contain 0 for ID key. 
	     */
		$needles = array(
			'category'   => array((int) $id),
		    'categories' => array(0)
		);

		//Create the link
		$link = 'index.php?option=com_vipquotes&view=quote&id='. $id;
		if ($catid > 1) {
			$categories = JCategories::getInstance('VipQuotes');
			$category   = $categories->get($catid);

			if($category) {
				$needles['category']   = array_reverse($category->getPath());
//				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		// Looking for menu item (Itemid)
		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		} elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * 
	 * Routing a link for category or categories view
	 * @param integer $catid
	 */
	public static function getCategoryRoute($catid) {
	    
		if ($catid instanceof JCategoryNode) {
			$id       = $catid->id;
			$category = $catid;
		} else {
			$id = (int) $catid;
			$category = JCategories::getInstance('VipQuotes')->get($id);
		}

		if ($id < 1) {
			$link = '';
		} else {
			$needles = array(
				'category' => array($id)
			);

			// Get menu item ( Itemid )
			if ($item = self::_findItem($needles)) {
				$link = 'index.php?Itemid='.$item;
			
			} else { // Continue to search and deep inside
			    
				//Create the link
				$link = 'index.php?option=com_vipquotes&view=category&id='.$id;

				if ($category) {
					$catids  = array_reverse($category->getPath());
					
					$needles = array(
						'category'   => $catids,
						'categories' => $catids
					);
					
					// Looking for menu item (Itemid)
					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					} elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}
	
	protected static function _findItem($needles = null) {
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		// Collect all menu items and creat an array that contains 
		// the ID from the query string of the menu item as a key, 
		// and the menu item id (Itemid) as a value
		// Example:
		// array( "category" => 
		//     1(id) => 100 (Itemid),
		//     2(id) => 101 (Itemid)
		// );
		if (self::$lookup === null) {
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_vipquotes');
			$items		= $menus->getItems('component_id', $component->id);

			if ($items) {
				foreach ($items as $item) {
					if (isset($item->query) && isset($item->query['view'])) {
						$view = $item->query['view'];

						if (!isset(self::$lookup[$view])) {
							self::$lookup[$view] = array();
						}

						if (isset($item->query['id'])) {
							self::$lookup[$view][$item->query['id']] = $item->id;
						} else { // If it is a root element that have no a request parameter ID ( categories ), we set 0 for an key
					        self::$lookup[$view][0] = $item->id;
						}
					}
				}
			}
		}

		if ($needles) {
		    
			foreach ($needles as $view => $ids) {
				if (isset(self::$lookup[$view])) {
					
				    foreach($ids as $id) {
						if (isset(self::$lookup[$view][(int)$id])) {
							return self::$lookup[$view][(int)$id];
						}
					}
					
				}
			}
			
		} else {
			$active = $menus->getActive();
			if ($active) {
				return $active->id;
			}
		}

		return null;
	}
	
	/**
	 * 
	 * Prepeare categories path to the segments.
	 * We use this method in the router "VipQuotesParseRoute".
	 * 
	 * @param integer   $catId		Category Id
	 * @param array 	$segments	
	 * @param integer 	$mId 		Id parameter from the menu item query
	 */
	public static function prepareCategoriesSegments($catId, &$segments, $mId = null) {
	    
	    $menuCatid    = $mId;
		$categories   = JCategories::getInstance('VipQuotes');
		$category     = $categories->get($catId);

		if ($category) {
			//TODO Throw error that the category either not exists or is unpublished
			$path = $category->getPath();
			$path = array_reverse($path);

			$array = array();
			foreach($path as $id) {
				if ((int)$id == (int)$mId) {
					break;
				}

				$array[] = $id;
			}
			$segments = array_merge($segments, array_reverse($array));
		}
	}
	
    
    /**
     * 
     * Load an object that contains a data about quote.
     * We use this method in the router "VipQuotesParseRoute".
     * 
     * @param integer $id
     */
    public static function getQuote($id) {
        
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        
        $query
            ->select("catid")
            ->from("#__vq_qutoes")
            ->where("id = " . $db->quote($id));

        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        
        if(!$result) {
            $result = null;
        }
        
        return $result;
			
    }
}
