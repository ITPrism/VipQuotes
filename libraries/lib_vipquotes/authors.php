<?php
/**
 * @package      VipQuotes
 * @subpackage   Authors
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage authors.
 *
 * @package      VipQuotes
 * @subpackage   Authors
 */
class VipQuotesAuthors implements Iterator, Countable, ArrayAccess
{
    protected $items = array();
    protected $columns = array("a.id", "a.name", "a.alias", "a.bio", "a.image", "a.thumb", "a.copyright", "a.hits", "a.ordering", "a.published");

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
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver  $db Database Driver
     */
    public function __construct(JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Set a list with columns that should be loaded.
     *
     * <code>
     * $columns = array("a.id", "a.name", "a.alias", "a.bio");
     *
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * $authors->setColumns($columns);
     * </code>
     *
     * @param array $columns
     * @return self
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Set a list with items.
     *
     * <code>
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * $authors->setItems($items);
     * </code>
     *
     * @param array $items
     *
     * @return self
     */
    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Load data about authors from database.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $options = array(
     *     "state" => 1 // 1 for published authors, 0 for not published authors, null for both.
     * );
     *
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * $authors->load($ids, $options);
     * </code>
     *
     * @param array $ids
     * @param array $options
     *
     * @throws UnexpectedValueException
     */
    public function load($ids = array(), $options = array())
    {
        if (!is_array($ids)) {
            throw new UnexpectedValueException(JText::_("LIB_VIPQUOTES_ERROR_AUTHORS_IDS_ARRAY"));
        }

        // Load data
        $query = $this->db->getQuery(true);

        $query
            ->select(implode(",", $this->columns))
            ->from($this->db->quoteName("#__vq_authors", "a"));

        if (!empty($ids)) {
            $query->where("a.id IN ( " . implode(",", $ids) . " )");
        }

        // Filter by state published.
        $state      = JArrayHelper::getValue($options, "state", 0, "int");
        $queryState = $this->getQueryStateString($state);
        if (!empty($queryState)) {
            $query->where($queryState);
        }

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        if (!$results) {
            $results = array();
        }

        $this->items = $results;
    }

    /**
     * Prepare query state string.
     *
     * @param NULL|integer $state
     *
     * @return string
     */
    protected function getQueryStateString($state)
    {
        $result = "";

        if (is_null($state)) { // All
            $result = "a.published IN (0, 1)";
        } elseif ($state == 0) { // Unpublished
            $result = "a.published = 0";
        } elseif ($state == 1) { // Published
            $result = "a.published = 1";
        }

        return $result;
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

    /**
     * Return the number of quotes for current authors.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * $authors->load($ids);
     *
     * $quotesNumber = $authors->getQuotesNumber();
     * </code>
     *
     * @return array
     */
    public function getQuotesNumber()
    {
        $ids = $this->getIds();
        if (!$ids) {
            return array();
        }

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.author_id, COUNT(*) AS number")
            ->from($this->db->quoteName("#__vq_quotes", "a"))
            ->where("a.author_id IN (" . implode(",", $ids) . ")")
            ->group("a.author_id");

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList("author_id", "number");

        if (!$results) {
            $results = null;
        }

        return $results;
    }

    /**
     * Return the number of quotes for current authors.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * $authors->load();
     *
     * $ids = $authors->getIds();
     * </code>
     *
     * @return array
     */
    public function getIds()
    {
        $ids = array();

        foreach ($this->items as $item) {
            $ids[] = $item->id;
        }

        return $ids;
    }

    /**
     * Return data about authors as options.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * $authors->load();
     *
     * $options = $authors->getOptions();
     * </code>
     *
     * @return array
     */
    public function getOptions()
    {
        $options = array();

        foreach ($this->items as $item) {
            $options[] = array("value" => $item->id, "text" => $item->name);
        }

        return $options;
    }

    /**
     * Return authors names.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $authors = new VipQuotesAuthors(JFactory::getDbo());
     * $authors->load();
     *
     * $names = $authors->getNames();
     * </code>
     *
     * @return array
     */
    public function getNames()
    {
        $names = array();

        foreach ($this->items as $item) {
            $names[] = $item->name;
        }

        return $names;
    }
}
