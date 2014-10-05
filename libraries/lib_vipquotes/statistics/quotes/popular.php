<?php
/**
 * @package      VipQuotes
 * @subpackage   Statistics\Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport("vipquotes.statistics.quotes");

/**
 * This class loads statistics about popular quotes.
 *
 * @package      VipQuotes
 * @subpackage   Statistics\Quotes
 */
class VipQuotesStatisticsQuotesPopular extends VipQuotesStatisticsQuotes implements Iterator, Countable, ArrayAccess
{
    public $data = array();

    protected $position = 0;

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
        $state = JArrayHelper::getValue($options, "state");
        if (is_numeric($state)) {
            $query->where("a.published = " . (int)$state);
        } else {
            $query->where("a.published IN (0,1)");
        }

        // Filter by author ID.
        $authorId = JArrayHelper::getValue($options, "author_id");
        if (!empty($authorId)) {
            $query->where("a.author_id = " . (int)$authorId);
        }

        // Filter by category ID.
        $categoryId = JArrayHelper::getValue($options, "category_id");
        if (!empty($categoryId)) {
            $query->where("a.catid = " . (int)$categoryId);
        }

        $query->order("a.hits DESC");

        $this->db->setQuery($query, 0, (int)$limit);

        $this->data = $this->db->loadAssocList();

        if (!$this->data) {
            $this->data = array();
        }
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return (!isset($this->data[$this->position])) ? null : $this->data[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->data[$this->position]);
    }

    public function count()
    {
        return (int)count($this->data);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}
