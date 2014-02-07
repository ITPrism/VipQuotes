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
 * This class provieds functionality that manage authors.
 */
class VipQuotesAthours implements Iterator, Countable, ArrayAccess {
    
    protected $items   = array();
    protected $columns = array("a.id", "a.name", "a.alias", "a.bio", "a.image", "a.thumb", "a.copyright", "a.hits", "a.ordering", "a.pubished");
    
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
     * @param JDatabase Database object.
     */
    public function __construct(JDatabase $db) {
        $this->db = $db;
    }

    public function setColumns(array $columns) {
        $this->columns = $columns;
    }
    
    public function setItems(array $items) {
        $this->items = $items;
        return $this;
    }
    
    public function load($ids = array(), $options = array()) {
        
        if(!is_array($ids)) {
            throw new UnexpectedValueException(JText::_("LIB_VIPQUOTES_ERROR_AUTHORS_IDS_ARRAY"));
        }
        
        // Load data
        $query = $this->db->getQuery(true);
        
        $query
            ->select(implode(",", $this->columns))
            ->from($this->db->quoteName("#__vq_authors", "a"));
        
        if(!empty($ids)){
            $query->where("a.id IN ( " . implode(",", $ids) ." )");
        }
        
        // Filter by state published.
        $state      = JArrayHelper::getValue($options, "state", 0, "int");
        $queryState = $this->getQueryStateString($state);
        if(!empty($queryState)) {
            $query->where($queryState);
        }
        
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        
        if(!$results) {
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
    protected function getQueryStateString($state) {
        
        $result = "";
        
        if(is_null($state)) {// All
            $result = "a.published IN (0, 1)";
        } else if($state == 0) { // Unpublished
            $result = "a.published = 0";
        } else if($state == 1) { // Published
            $result = "a.published = 1";
        }
        
        return $result;
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
    
    /**
     * Count and return quotes number.
     *
     * @return array
     */
    public function getQuotesNumber() {
    
        $ids = $this->getIds();
        if(!$ids) {
            return array();
        }
    
        // Create a new query object.
        $query  = $this->db->getQuery(true);
    
        $query
            ->select("a.author_id, COUNT(*) AS number")
            ->from( $this->db->quoteName("#__vq_quotes", "a"))
            ->where("a.author_id IN (" .implode(",", $ids). ")")
            ->group("a.author_id");
            	
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList("author_id", "number");
         
        if(!$results) {
            $results = null;
        }
        
        return $results;
    }
    
    public function getIds() {
        
        $ids = array();
        
        foreach($this->items as $item) {
            $ids[] = $item->id;
        }
        
        return $ids;
    }
    
    public function getOptions() {
        
        $options = array();
        
        foreach($this->items as $item) {
            $options[] = array("value" => $item->id, "text" => $item->name);
        }
        
        return $options;
    }
}
