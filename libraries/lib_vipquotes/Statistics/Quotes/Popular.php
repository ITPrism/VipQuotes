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
 * This class loads statistics about popular quotes.
 *
 * @package      VipQuotes
 * @subpackage   Statistics\Quotes
 */
class Popular extends Quotes
{
    /**
     * Aggregate data about popular quotes from database.
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

        $query->order("a.hits DESC");

        $this->db->setQuery($query, 0, (int)$limit);

        $this->items = (array)$this->db->loadAssocList();
    }
}
