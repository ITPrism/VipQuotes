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
 * This class loads statistics about transactions.
 */
class VipQuotesStatisticsBasic {
    
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
    
    public function getTotalQuotes() {
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__vq_quotes", "a"));
        
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
        
        if(!$result) {
            $result = 0;
        }
        
        return $result;
        
    }
    
    public function getTotalAuthors() {
    
        $query = $this->db->getQuery(true);
    
        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__vq_authors", "a"));
    
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
    
        if(!$result) {
            $result = 0;
        }
    
        return $result;
    
    }
    
}
