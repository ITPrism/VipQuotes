<?php
/**
 * @package      VipQuotes
 * @subpackage   Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Quote;

use Prism\Database\ArrayObject;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage quotes.
 *
 * @package      VipQuotes
 * @subpackage   Quotes
 */
class Quotes extends ArrayObject
{
    /**
     * Load data about quotes from database.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $quotes = new VipQuotesQuotes(JFactory::getDbo());
     * $quotes->load($ids);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $ids = ArrayHelper::getValue($options, "ids");
        $ids = ArrayHelper::toInteger($ids);

        $authorId = ArrayHelper::getValue($options, "author_id", 0, "int");
        $categoryId = ArrayHelper::getValue($options, "category_id", 0, "int");

        $query = $this->getQuery();

        // Filter by quotes IDs.
        if (!empty($ids)) {
            $query->where("a.id IN (" . implode(",", $ids) . ")");
        }

        // Filter by category ID.
        if (!empty($categoryId)) {
            $query->where("a.catid = " . (int)$categoryId);
        }

        // Filter by author ID.
        if (!empty($authorId)) {
            $query->where("a.author_id = " . (int)$authorId);
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
    }

    protected function getQuery()
    {
        $query = $this->db->getQuery(true);

        $query->select(
            "a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id, " .
            "b.name AS author, " .
            "c.title AS category, " .
            "d.name AS user"
        );
        $query->select($query->concatenate(array("b.id", "b.alias"), "-") . " AS authorslug");
        $query->select($query->concatenate(array("c.id", "c.alias"), "-") . " AS catslug");

        $query->from($this->db->quoteName("#__vq_quotes", "a"));
        $query->leftJoin($this->db->quoteName("#__vq_authors", "b") . " ON a.author_id = b.id");
        $query->leftJoin($this->db->quoteName("#__categories", "c") . " ON a.catid = c.id");
        $query->leftJoin($this->db->quoteName("#__users", "d") . " ON a.user_id = d.id");

        return $query;
    }
}
