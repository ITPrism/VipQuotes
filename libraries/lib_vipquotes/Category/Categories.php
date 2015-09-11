<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die;
jimport('joomla.application.categories');

class VipQuotesCategories extends JCategories
{
    public function __construct($options = array())
    {
        $options['table']     = '#__vq_quotes';
        $options['extension'] = 'com_vipquotes';
        parent::__construct($options);
    }

    public static function getCategories()
    {
        $db = JFactory::getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("id, title")
            ->from("#__categories")
            ->where($db->quoteName("extension") . " = " . $db->quote("com_vipquotes"));

        $db->setQuery($query);

        return $db->loadAssocList("id", "title");
    }
}
