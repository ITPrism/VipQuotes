<?php
/**
 * @package      VipQuotes
 * @subpackage   Authors
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Author;

use Prism\Database\ArrayObject;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage authors.
 *
 * @package      VipQuotes
 * @subpackage   Authors
 */
class Authors extends ArrayObject
{
    protected $columns = array("a.id", "a.name", "a.alias", "a.bio", "a.image", "a.thumb", "a.copyright", "a.hits", "a.ordering", "a.published");

    /**
     * Set a list with columns that should be loaded.
     *
     * <code>
     * $columns = array("a.id", "a.name", "a.alias", "a.bio");
     *
     * $authors = new VipQuotes\Author\Authors(\JFactory::getDbo());
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
     * $authors = new VipQuotes\Author\Authors(\JFactory::getDbo());
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
     * $authors = new VipQuotes\Author\Authors(\JFactory::getDbo());
     * $authors->load($ids, $options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $ids = ArrayHelper::getValue($options, "ids", array(), "array");
        $ids = ArrayHelper::toInteger($ids);

        // Load data
        $query = $this->db->getQuery(true);

        $query
            ->select(implode(",", $this->columns))
            ->from($this->db->quoteName("#__vq_authors", "a"));

        // Filter by authors IDs.
        if (!empty($ids)) {
            $query->where("a.id IN ( " . implode(",", $ids) . " )");
        }

        // Filter by state.
        $state      = ArrayHelper::getValue($options, "state", 0, "int");
        $queryState = $this->getQueryStateString($state);
        if (!empty($queryState)) {
            $query->where($queryState);
        }

        $this->db->setQuery($query);
        $this->items = (array)$this->db->loadAssocList();
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

    /**
     * Return the number of quotes for current authors.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $authors = new VipQuotes\Author\Authors(\JFactory::getDbo());
     * $authors->load($ids);
     *
     * $quotesNumber = $authors->getQuotesNumber();
     * </code>
     *
     * @return array
     */
    public function getQuotesNumber()
    {
        $results = array();
        
        $ids = $this->getKeys();

        if (!empty($ids)) {

            // Create a new query object.
            $query = $this->db->getQuery(true);

            $query
                ->select("a.author_id, COUNT(*) AS number")
                ->from($this->db->quoteName("#__vq_quotes", "a"))
                ->where("a.author_id IN (" . implode(",", $ids) . ")")
                ->group("a.author_id");

            $this->db->setQuery($query);
            $results = (array)$this->db->loadAssocList("author_id", "number");
        }

        return $results;
    }

    /**
     * Return authors names.
     *
     * <code>
     * $ids = array(1, 2, 3, 4, 5);
     *
     * $authors = new VipQuotes\Author\Authors(\JFactory::getDbo());
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
