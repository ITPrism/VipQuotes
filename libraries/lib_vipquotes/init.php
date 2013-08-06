<?php
/**
 * @package      Vip Quotes
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

if(!defined("VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR")) {
    define("VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_vipquotes");
}

if(!defined("VIPQUOTES_PATH_COMPONENT_SITE")) {
    define("VIPQUOTES_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_vipquotes");
}

if(!defined("VIPQUOTES_PATH_LIBRARY")) {
    define("VIPQUOTES_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "vipquotes");
}

if(!defined("ITPRISM_PATH_LIBRARY")) {
    define("ITPRISM_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "itprism");
}

jimport('joomla.utilities.arrayhelper');

// Register Component libraries
JLoader::register("ITPrismErrors",    ITPRISM_PATH_LIBRARY. DIRECTORY_SEPARATOR ."errors.php");
JLoader::register("VipQuotesVersion", VIPQUOTES_PATH_LIBRARY .DIRECTORY_SEPARATOR ."version.php");

// Register helpers
JLoader::register("VipQuotesHelper", VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "vipquotes.php");
JLoader::register("VipQuotesCategories", VIPQUOTES_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "category.php");
JLoader::register("VipQuotesHelperRoute", VIPQUOTES_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");

