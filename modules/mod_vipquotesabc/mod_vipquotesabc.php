<?php
/**
 * @package      Vip Quotes
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor.iliev@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( "_JEXEC" ) or die;

JLoader::register("VipQuotesHelperRoute", JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vipquotes".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."route.php");

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$currentView   = $app->input->get("view");
$currentOption = $app->input->get("option");

if( (strcmp("com_vipquotes", $currentOption) == 0) AND (strcmp("quotes", $currentView) == 0)) {
    $alphaValue = $app->getUserStateFromRequest("com_vipquotes.quotes.filter.alpha", "filter_alpha", null, "cmd");
    $alphaValue = htmlentities(JString::trim($alphaValue), ENT_QUOTES, "UTF-8");
} else {
    $alphaValue = null;
}

$alphas = range('A', 'Z');
$link   = VipQuotesHelperRoute::getQuotesRoute();

$filterAuthor       = $params->get("filter_authors", 0);
$filterCategories   = $params->get("filter_categories", 0);

if(!$filterAuthor AND !$filterCategories) {
   return;   
}

if($filterAuthor AND $filterCategories) {
    $tooltip = JText::_("MOD_VIPQUOTESABC_ALPHABET_FILTER_TOOLTIP_ALL");
} else if($filterAuthor) {
    $tooltip = JText::_("MOD_VIPQUOTESABC_ALPHABET_FILTER_TOOLTIP_AUTHORS");
} else if($filterCategories) {
    $tooltip = JText::_("MOD_VIPQUOTESABC_ALPHABET_FILTER_TOOLTIP_CATEGORIES");
}

require JModuleHelper::getLayoutPath('mod_vipquotesabc', $params->get('layout', 'default'));