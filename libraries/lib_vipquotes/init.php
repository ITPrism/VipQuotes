<?php
/**
 * @package      VipQuotes
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (!defined("VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR")) {
    define("VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_vipquotes");
}

if (!defined("VIPQUOTES_PATH_COMPONENT_SITE")) {
    define("VIPQUOTES_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_vipquotes");
}

if (!defined("VIPQUOTES_PATH_LIBRARY")) {
    define("VIPQUOTES_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "VipQuotes");
}

JLoader::registerNamespace('VipQuotes', JPATH_LIBRARIES);

// Register some helpers
JLoader::register("VipQuotesHelper", VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR . "/helpers/vipquotes.php");
JLoader::register("VipQuotesHelperRoute", VIPQUOTES_PATH_COMPONENT_SITE . "/helpers/route.php");

// Register class aliases.
JLoader::registerAlias('VipQuotesCategories', '\\VipQuotes\\Categories');

// Register Observers
JLoader::register("VipQuotesObserverAuthor", VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR ."/tables/observers/author.php");
JObserverMapper::addObserverClassToClass('VipQuotesObserverAuthor', 'VipQuotesTableAuthor', array('typeAlias' => 'com_vipquotes.author'));

// Include HTML helpers path
JHtml::addIncludePath(VIPQUOTES_PATH_COMPONENT_SITE . '/helpers/html');

// Load library language
$lang = JFactory::getLanguage();
$lang->load('lib_vipquotes', VIPQUOTES_PATH_LIBRARY);
