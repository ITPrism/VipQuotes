<?php
/**
 * @package      VipQuotes
 * @subpackage   Statistics\Authors
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Statistics\Authors;

use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class loads statistics about popular authors.
 *
 * @package      VipQuotes
 * @subpackage   Statistics\Authors
 */
class Popular extends Authors
{
    /**
     * Aggregate data about popular authors from database.
     *
     * <code>
     * $options = array(
     *     "state" = 1 // 1 for published, 0 for not published, null for both.
     * );
     * $statistics = new VipQuotes\Statistics\Authors\Popular(\JFactory::getDbo());
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

        $query->order("a.hits DESC");

        $this->db->setQuery($query, 0, (int)$limit);

        $this->items = (array)$this->db->loadAssocList();
    }
}
