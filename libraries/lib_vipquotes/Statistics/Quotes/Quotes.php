<?php
/**
 * @package      VipQuotes
 * @subpackage   Statistics\Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Statistics\Quotes;

use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This is a base class for quotes statistics.
 *
 * @package      VipQuotes
 * @subpackage   Statistics\Quotes
 */
abstract class Quotes extends ArrayObject
{
    protected function getQuery()
    {
        $query = $this->db->getQuery(true);

        $query->select(
            "a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id, " .
            "b.name AS author, b.image, b.thumb, " .
            "c.title AS category"
        );
        $query->select($query->concatenate(array("b.id", "b.alias"), "-") . " AS authorslug");
        $query->select($query->concatenate(array("c.id", "c.alias"), "-") . " AS catslug");

        $query->from($this->db->quoteName("#__vq_quotes", "a"));
        $query->leftJoin($this->db->quoteName("#__vq_authors", "b") . " ON a.author_id = b.id");
        $query->leftJoin($this->db->quoteName("#__categories", "c") . " ON a.catid = c.id");

        return $query;
    }
}
