<?php
/**
 * @package      VipQuotes
 * @subpackage   Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage quotes.
 *
 * @package      VipQuotes
 * @subpackage   Quotes
 */
class VipQuotesQuotes implements Iterator, Countable, ArrayAccess
{
    protected $items = array();

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    protected $position = 0;

    /**
     * Initialize the object.
     *
     * <code>
     * $quotes = new VipQuotesQuotes(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver  $db Database Driver
     */
    public function __construct(JDatabaseDriver $db)
    {
        $this->db = $db;
    }

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
     * @param array $ids
     */
    public function load($ids)
    {
        $query = $this->getQuery();

        $query->where("a.id IN (" . implode(",", $ids) . ")");

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Load all quotes for an author.
     *
     * <code>
     * $authorId = 1;
     *
     * $quotes = new VipQuotesQuotes(JFactory::getDbo());
     * $quotes->loadAuthorQuotes($authorId);
     * </code>
     *
     * @param int $id
     */
    public function loadAuthorQuotes($id)
    {
        $query = $this->getQuery();

        $query->where("a.author_id = " . (int)$id);

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Load all quotes for in a category.
     *
     * <code>
     * $categoryId = 1;
     *
     * $quotes = new VipQuotesQuotes(JFactory::getDbo());
     * $quotes->loadCategoryQuotes($categoryId);
     * </code>
     *
     * @param int $id
     */
    public function loadCategoryQuotes($id)
    {
        $query = $this->getQuery();

        $query->where("a.catid = " . (int)$id);

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
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

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return (!isset($this->items[$this->position])) ? null : $this->items[$this->position];
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
        return isset($this->items[$this->position]);
    }

    public function count()
    {
        return (int)count($this->items);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
}
