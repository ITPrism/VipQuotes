<?php
/**
 * @package      ITPrism Components
 * @subpackage   Vip Quotes 
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

jimport('joomla.utilities.arrayhelper');

if(!defined("VIPQUOTES_COMPONENT_ADMINISTRATOR")) {
    define("VIPQUOTES_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_vipquotes");
}

// Register Component libraries
JLoader::register("VipQuotesErrors", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . "errors.php");
JLoader::register("VipQuotesVersion", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . "version.php");
JLoader::register("VipQuotesFileUpload", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . "upload.php");
JLoader::register("VipQuotesFileUploadImage", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . "image.php");
JLoader::register("VipQuotesSimpleXml", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . "simplexml.php");

// Controllers
JLoader::register("VipQuotesControllerAdmin", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . "controller". DIRECTORY_SEPARATOR . "admin.php");
JLoader::register("VipQuotesControllerAdminForm", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "libraries" . DIRECTORY_SEPARATOR . "controller". DIRECTORY_SEPARATOR . "adminform.php");

// Register helpers
JLoader::register("VipQuotesHelper", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "vipquotes.php");
JLoader::register("VipQuotesCategories", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "category.php");
JLoader::register("VipQuotesHelperRoute", VIPQUOTES_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");

