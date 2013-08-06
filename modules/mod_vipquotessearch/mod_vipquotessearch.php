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

// no direct access
defined( "_JEXEC" ) or die;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

jimport("vipquotes.init");
JLoader::register("VipQuotesHelperRoute", JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vipquotes".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."route.php");

$app = JFactory::getApplication();
/** @var $app JSite **/

$phraseValue = JString::trim($app->input->get("filter_phrase"));
$phraseValue = htmlentities($phraseValue, ENT_QUOTES, "UTF-8");

if($params->get("display_categories", 0)) {
    $config = array(
        "filter.published" => 1
    );
    $categories = JHtml::_("category.categories", "com_vipquotes", $config);
    
    $option   = JHtml::_("select.option", 0, JText::_("COM_VIPQUOTES_SELECT_CATEGORY"));
    $option   = array($option);
    
    $categories = array_merge($option, $categories);
    
    $lastItem = end($categories);
    if($lastItem->value == 1) {
        array_pop($categories);
    }
    
    $categoryValue = $app->input->get("filter_category", 0, "int");
}

require JModuleHelper::getLayoutPath('mod_vipquotessearch', $params->get('layout', 'default'));