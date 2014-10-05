<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * The class contains statics helper function.
 */
class VipQuotesHelper
{
    public static $extension = 'com_vipquotes';

    public static $categoriesAliases = null;
    public static $categories = null;

    public static $categoriesQuotesNumber = null;

    /**
     * Configure the Linkbar.
     *
     * @param    string $vName   The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_VIPQUOTES_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPQUOTES_CATEGORIES'),
            'index.php?option=com_categories&extension=' . self::$extension,
            $vName == 'categories'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPQUOTES_AUTHORS'),
            'index.php?option=' . self::$extension . '&view=authors',
            $vName == 'authors'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPQUOTES_QUOTES'),
            'index.php?option=' . self::$extension . '&amp;view=quotes',
            $vName == 'quotes'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPQUOTES_EMAILS'),
            'index.php?option=' . self::$extension . '&amp;view=emails',
            $vName == 'emails'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPQUOTES_FACEBOOK_PAGES'),
            'index.php?option=' . self::$extension . '&view=pages',
            $vName == 'pages'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_VIPQUOTES_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode("vip quotes"),
            $vName == 'plugins'
        );
    }

    public static function getFacebookPageName($pageId)
    {
        $db = JFactory::getDBO();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query->select("title")
            ->from("#__vq_pages")
            ->where("page_id =" . $db->quote($pageId));

        $db->setQuery($query, 0, 1);
        $name = $db->loadResult();

        return $name;
    }

    /**
     * Make a request to facebook and get pages
     *
     * @param Facebook $facebook
     *
     * @return array
     */
    public static function getFacebookPages($facebook)
    {
        $accounts = $facebook->api("/me/accounts");
        $accounts = JArrayHelper::getValue($accounts, "data");

        $pages = array();

        if (!empty($accounts)) {

            // Get only pages and exclude applications
            foreach ($accounts as $account) {
                if (strcmp("Application", $account["category"])) {
                    $pages[] = $account;
                }
            }
        }

        return $pages;
    }

    public static function getFacebookPageAccessToken($facebook, $pageId)
    {
        $accessToken = "";
        $pages       = self::getFacebookPages($facebook);

        foreach ($pages as $page) {
            if ($pageId == $page["id"]) {
                $accessToken = $page["access_token"];
                break;
            }
        }

        return $accessToken;
    }

    /**
     * @param JDocument $document
     * @param Joomla\Registry\Registry $params
     */
    public static function facebookAutoGrow($document, $params)
    {
        $js = 'window.fbAsyncInit = function() {
    	  FB.init({ 
  	        appId: "' . $params->get("fbpp_app_id", "") . '",
  	        cookie : true, 
  	        status : true, 
  	        xfbml  : true,
  	        oauth  : true
  	     });

    	  FB.Canvas.setAutoGrow();
    	  
      };

      // Load the SDK Asynchronously
      (function(d){
         var js, id = "facebook-jssdk"; if (d.getElementById(id)) {return;}
         js = d.createElement("script"); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         d.getElementsByTagName("head")[0].appendChild(js);
       }(document));';

        $document->addScriptDeclaration($js);
    }

    public static function getCategories($index = "id")
    {
        if (is_null(self::$categories)) {

            $db = JFactory::getDbo();
            /** @var $db JDatabaseDriver */

            // Create a new query object.
            $query = $db->getQuery(true);

            // Select the required fields from the table.
            $query
                ->select('a.id, a.title, a.alias')
                ->from($db->quoteName("#__categories") . ' AS a')
                ->where('a.extension = "com_vipquotes"')
                ->where('a.published = 1');

            $db->setQuery($query);

            self::$categories = $db->loadAssocList($index);

        }

        return self::$categories;
    }

    public static function getSubCategories($parentId)
    {
        $db = JFactory::getDbo();
        /** @var $db JDatabaseDriver */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select('a.id, a.title, a.params, ' . $query->concatenate(array("a.id", "a.alias"), ":") . " AS slug")
            ->from($db->quoteName("#__categories") . ' AS a')
            ->where('a.extension = "com_vipquotes"')
            ->where('a.published = 1')
            ->where('a.parent_id = ' . (int)$parentId);

        $db->setQuery($query);

        $result = $db->loadObjectList("id");
        if (!empty($result)) {

            foreach ($result as $key => $item) {
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
     * @param $state
     *
     * @return array
     */
    public static function getCategoryQuotesNumber($state = null)
    {
        if (is_null(self::$categoriesQuotesNumber)) {

            $db = JFactory::getDbo();
            /** @var $db JDatabaseMySQLi * */

            $query = $db->getQuery(true);

            $query
                ->select("a.catid, COUNT(*) AS number")
                ->from($db->quoteName("#__vq_quotes") . ' AS a')
                ->group("a.catid");

            if (is_null($state)) { // All
                $query->where("a.published IN (0, 1)");
            } elseif ($state == 0) { // Unpublished
                $query->where("a.published = 0");
            } elseif ($state == 1) { // Published
                $query->where("a.published = 1");
            }

            $db->setQuery($query);
            $results = $db->loadAssocList("catid", "number");

            if (!$results) {
                $results = array();
            }

            self::$categoriesQuotesNumber = $results;
        }

        return self::$categoriesQuotesNumber;
    }

    public static function getImage($type, $item, $imagesDirectory, $defaultImage = "no_image.png")
    {
        $image = "";

        // Prepare image
        switch ($type) {

            case "large":
                $image = (!$item["image"]) ? "media/com_vipquotes/images/" . $defaultImage : $imagesDirectory . "/" . $item["image"];
                break;

            case "thumb":
                $image = (!$item["thumb"]) ? "media/com_vipquotes/images/" . $defaultImage : $imagesDirectory . "/" . $item["thumb"];
                break;

            default: // none

                break;
        }

        return $image;
    }

    public static function calculateSpanValue($numberOfResults, $limit)
    {
        if ($limit > $numberOfResults) {
            if ($numberOfResults <= 0) {
                $itemSpan = 12;
            } else {
                $itemSpan = round(12 / $numberOfResults);
            }
        } else {
            $itemSpan = round(12 / $limit);
        }

        return $itemSpan;
    }
}
