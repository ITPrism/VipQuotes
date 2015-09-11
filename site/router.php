<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('VipQuotes.init');
jimport('joomla.application.categories');

/**
 * Method to build Route
 *
 * @param array $query
 *
 * @return array
 */
function VipQuotesBuildRoute(&$query)
{
    $segments = array();

    // get a menu item based on Itemid or currently active
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();

    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if (empty($query['Itemid'])) {
        $menuItem = $menu->getActive();
    } else {
        $menuItem = $menu->getItem($query['Itemid']);
    }

    $mOption = (empty($menuItem->query['option'])) ? null : $menuItem->query['option'];
    $mView   = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
//    $mCatid  = (empty($menuItem->query['catid'])) ? null : $menuItem->query['catid'];
    $mId     = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

    // If is set view and Itemid missing, we have to put the view to the segments
    if (isset($query['view'])) {
        $view = $query['view'];

        if (empty($query['Itemid']) or ($mOption !== "com_vipquotes")) {
            $segments[] = $query['view'];
        }

        // We need to keep the view for forms since they never have their own menu item
        if ($view != 'form' or $view != 'authorform') {
            unset($query['view']);
        }
    };

    // are we dealing with a category or author that is attached to a menu item?
    if (isset($view) and ($mView == $view) and (isset($query['id'])) and ($mId == intval($query['id']))) {
        unset($query['view']);
        unset($query['catid']);
        unset($query['id']);

        return $segments;
    }

    // Views
    if (isset($view)) {

        switch ($view) {

            case "category":

                if ($mId != intval($query['id']) || $mView != $view) {
                    $catId = $query['id'];

                    VipQuotesHelperRoute::prepareCategoriesSegments($catId, $segments, $mId);
                    unset($query['id']);
                    unset($query['catid']);
                }

                break;


            case "author":

                if (isset($query["id"])) {
                    $segments[] = $query["id"];
                    unset($query["id"]);
                }
                break;

            case "quote":

                $catId = 0;

                if (!isset($query['catid'])) {
                    if ($menuItem->query["view"] == "category") {
                        $catId = $menuItem->query["id"];
                    }
                } else {
                    $catId = $query['catid'];
                }

                VipQuotesHelperRoute::prepareCategoriesSegments($catId, $segments, $mId);

                $id         = $query['id'];
                $segments[] = $id;

                unset($query['id']);
                unset($query['catid']);
                break;

            case "form":
            case "authorform":
                if ($menuItem->query["view"] == $view) {
                    unset($query['view']);
                }
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
 *
 * @param array $segments
 *
 * @return array
 */
function VipQuotesParseRoute($segments)
{
    $vars = array();

    //Get the active menu item.
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();
    $item = $menu->getActive();

    // Count route segments
    $count = count($segments);

    // Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
    // the first segment is the view and the last segment is the id of the quote, category or author.
    if (!isset($item)) {
        $vars['view']  = $segments[0];
        $vars['catid'] = $segments[$count - 1];

        return $vars;
    }

    // if there is only one segment, then it points to either an quote, author or a category
    // we test it first to see if it is a category.  If the id and alias match a category
    // then we assume it is a category.  If they don't we assume it is an quote
    if ($count == 1) {

        // We check if it is a quote.
        // Only quotes ids are true numeric.
        // So, it is a quote.
        if (is_numeric($segments[0])) {

            $id = $segments[0];

            $quote = VipQuotesHelperRoute::getQuote($id);
            if ($quote) {
                $vars['view']  = 'quote';
                $vars['catid'] = (int)$quote->catid;
                $vars['id']    = (int)$id;

                return $vars;
            }

        }

        list($id, $alias) = explode(':', $segments[0], 2);

        // First we check if it is a category
        $category = JCategories::getInstance('VipQuotes')->get($id);
        if ($category && $category->alias == $alias) {
            $vars['view'] = 'category';
            $vars['id']   = $id;

            return $vars;

        }

        // Second we check if it is an author
        $author = VipQuotesHelperRoute::getAuthor($id);
        if ($author && $author->alias == $alias) {

            $vars['view'] = 'author';
            $vars['id']   = (int)$id;

            return $vars;
        }

    }

    // COUNT >= 2

    if ($count >= 2) {

        // We check if it is a quote.
        // Only quotes ids are true numeric.
        // So, it is a quote.
        if (is_numeric($segments[$count - 1])) {

            $id = $segments[$count - 1];

            $quote = VipQuotesHelperRoute::getQuote($id);
            if ($quote) {
                $vars['view']  = 'quote';
                $vars['catid'] = (int)$quote->catid;
                $vars['id']    = (int)$id;

                return $vars;
            }

        }

        list($id, $alias) = explode(':', $segments[$count - 1], 2);

        // First we check if it is a category
        $category = JCategories::getInstance('VipQuotes')->get($id);
        if ($category && $category->alias == $alias) {
            $vars['view'] = 'category';
            $vars['id']   = $id;

            return $vars;
        }

        // Check for author
        $author = VipQuotesHelperRoute::getAuthor($id);
        if (!empty($author) and ($author->alias == $alias)) {
            $vars['view'] = 'author';
            $vars['id']   = (int)$id;

            return $vars;
        }

    }

    return $vars;
}
