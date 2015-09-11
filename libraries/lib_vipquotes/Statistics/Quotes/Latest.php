<?php
/**
 * @package      VipQuotes
 * @subpackage   Statistics\Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Statistics\Quotes;

use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class loads statistics about latest quotes.
 *
 * @package      VipQuotes
 * @subpackage   Statistics\Quotes
 */
class Latest extends Quotes
{
    /**
     * Aggregate data about latest quotes from database.
     *
     * <code>
     * $options = array(
     *     "state" => 1, // 1 for published, 0 for not published, null for both.
     *      "category_id" => 2,
     *      "author_id" => 3,
     * );
     * $statistics = new VipQuotesStatisticsAuthorsPopular(JFactory::getDbo());
     *
     * $statistics->load();
     *
     * foreach ($statistics as $author) {
     * ...
     * }
     * </code>
     *
     * @param int   $limit
     * @param array $options
     */
    public function load($limit = 5, $options = array())
    {
        $query = $this->getQuery();

        // Filter by state.
        $state = ArrayHelper::getValue($options, "state");
        if (is_numeric($state)) {
            $query->where("a.published = " . (int)$state);
        } else {
            $query->where("a.published IN (0,1)");
        }

        // Filter by author ID.
        $authorId = ArrayHelper::getValue($options, "author_id");
        if (!empty($authorId)) {
            $query->where("a.author_id = " . (int)$authorId);
        }

        // Filter by category ID.
        $categoryId = ArrayHelper::getValue($options, "category_id");
        if (!empty($categoryId)) {
            $query->where("a.catid = " . (int)$categoryId);
        }

        // Set ordering.
        $query->order("a.created DESC");

        $this->db->setQuery($query, 0, (int)$limit);

        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Load latest quotes ordering by date of adding.
     *
     * @param int $limit The number of results.
     */
    public function loadByCreated($limit = 5)
    {
        $query = $this->getQuery();

        $query
            ->where("a.published = 1")
            ->where("a.approved = 1")
            ->order("a.created DESC");

        $this->db->setQuery($query, 0, (int)$limit);

        $this->items = $this->db->loadAssocList();

        if (!$this->items) {
            $this->items = array();
        }
    }
}
