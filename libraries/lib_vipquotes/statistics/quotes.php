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
 * This is a base class for quotes statistics.
 */
abstract class VipQuotesStatisticsQuotes {
    
    /**
     * Database driver
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    /**
     * Initialize the object.
     * 
     * @param JDatabase   Database Driver
     */
    public function __construct(JDatabase $db) {
        $this->db = $db;
    }

    protected function getQuery() {
        
        $query = $this->db->getQuery(true);
        
        $query->select(
                "a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id, " .
                "b.name AS author, b.image, b.thumb, " .
                "c.title AS category");
        $query->select($query->concatenate(array("b.id", "b.alias"), "-") . " AS authorslug");
        $query->select($query->concatenate(array("c.id", "c.alias"), "-") . " AS catslug");
        
        $query->from($this->db->quoteName("#__vq_quotes", "a"));
        $query->leftJoin($this->db->quoteName("#__vq_authors", "b") ." ON a.author_id = b.id");
        $query->leftJoin($this->db->quoteName("#__categories", "c") ." ON a.catid = c.id");
        
        return $query;
        
    }
    
}
