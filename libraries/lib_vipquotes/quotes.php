<?php
/**
* @package      VipQuotes
* @subpackage   Libraries
* @author       Todor Iliev
* @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;

/**
 * This class provieds functionality that manage quotes.
 */
class VipQuotesQuotes implements Iterator, Countable, ArrayAccess {
    
    protected $items   = array();
    
    /**
     * Database driver.
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected $position = 0;
    
    /**
     * Initialize the object.
     * 
     * @param JDatabase   $db
     */
    public function __construct(JDatabase $db) {
        $this->db   = $db;
    }

    public function load($ids) {
    
        $results = array();
    
        $query = $this->getQuery();
        
        $query->where("a.id IN (" .implode(",", $ids) .")");
    
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
    
        if(!$results) {
            $results = array();
        }
    
        $this->items = $results;
    }
    
    public function loadAuthorQuotes($id) {
        
        $results = array();
        
        $query = $this->getQuery();
        
        $query->where("a.author_id = " .(int)$id);
        
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        
        if(!$results) {
            $results = array();
        }
    
        $this->items = $results;
    }
    
    public function loadCategoryQuotes($id) {
    
        $results = array();
    
        $query = $this->getQuery();
    
        $query->where("a.catid = " .(int)$id);
    
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
    
        if(!$results) {
            $results = array();
        }
    
        $this->items = $results;
    }
    
    protected function getQuery() {
        
        $query = $this->db->getQuery(true);
        
        $query->select(
                "a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id, " .
                "b.name AS author, " .
                "c.title AS category, " .
                "d.name AS user");
        $query->select($query->concatenate(array("b.id", "b.alias"), "-") . " AS authorslug");
        $query->select($query->concatenate(array("c.id", "c.alias"), "-") . " AS catslug");
        
        $query->from($this->db->quoteName("#__vq_quotes", "a"));
        $query->leftJoin($this->db->quoteName("#__vq_authors", "b") ." ON a.author_id = b.id");
        $query->leftJoin($this->db->quoteName("#__categories", "c") ." ON a.catid = c.id");
        $query->leftJoin($this->db->quoteName("#__users", "d") ." ON a.user_id = d.id");
        
        return $query;
    }
    
    public function rewind() {
        $this->position = 0;
    }
    
    public function current() {
        return (!isset($this->items[$this->position])) ? null : $this->items[$this->position];
    }
    
    public function key() {
        return $this->position;
    }
    
    public function next() {
        ++$this->position;
    }
    
    public function valid() {
        return isset($this->items[$this->position]);
    }
    
    public function count() {
        return (int)count($this->items);
    }
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
}
