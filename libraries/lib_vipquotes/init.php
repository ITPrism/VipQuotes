<?php
/**
 * @package      VipQuotes
 * @subpackage   Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
    define("VIPQUOTES_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "vipquotes");
}

jimport('joomla.utilities.arrayhelper');

// Constants
JLoader::register("VipQuotesConstants", VIPQUOTES_PATH_LIBRARY . DIRECTORY_SEPARATOR . "constants.php");

// Register Component libraries
JLoader::register("VipQuotesVersion", VIPQUOTES_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");

// Register helpers
JLoader::register("VipQuotesHelper", VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "vipquotes.php");
JLoader::register("VipQuotesCategories", VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "category.php");
JLoader::register("VipQuotesHelperRoute", VIPQUOTES_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");

// Register Observers
JLoader::register("VipQuotesObserverAuthor", VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR ."/tables/observers/author.php");
JObserverMapper::addObserverClassToClass('VipQuotesObserverAuthor', 'VipQuotesTableAuthor', array('typeAlias' => 'com_vipquotes.author'));

// Register Facebook SDK 
JLoader::register("Facebook", VIPQUOTES_PATH_LIBRARY . DIRECTORY_SEPARATOR . "facebook" . DIRECTORY_SEPARATOR . "facebook.php");

// Include HTML helpers path
JHtml::addIncludePath(VIPQUOTES_PATH_COMPONENT_SITE . '/helpers/html');

// Load library language
$lang = JFactory::getLanguage();
$lang->load('lib_vipquotes', VIPQUOTES_PATH_LIBRARY);
