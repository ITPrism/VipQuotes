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
 * This class provieds functionality that manages filter options.
 */
class VipQuotesFilterOptions {
    
    protected $options  = array();
    
    /**
     * Database driver.
     * 
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    protected static $instance;
    
    /**
     * Initialize the object.
     * 
     * @param JDatabase Database object.
     */
    public function __construct(JDatabase $db) {
        $this->db = $db;
    }

    public static function getInstance(JDatabase $db)  {
    
        if (is_null(self::$instance)){
            self::$instance = new VipQuotesFilterOptions($db);
        }
    
        return self::$instance;
    }
    
    public function getAuthors($options = array()) {
       
        if(!isset($this->options["authors"])) {
            
            $query  = $this->db->getQuery(true);
            
            $query
                ->select("a.id AS value, a.name AS text")
                ->from($this->db->quoteName("#__vq_authors", "a"))
                ->order("a.name");

            // Filter by state.
            $state = JArrayHelper::getValue($options, "state");
            if(is_null($state)) {// All
                $query->where("a.published IN (0, 1)");
            } else if($state == 0) { // Unpublished
                $query->where("a.published = 0");
            } else if($state == 1) { // Published
                $query->where("a.published = 1");
            }
            
            $this->db->setQuery($query);
            $rows = $this->db->loadAssocList();
            
            if(!$rows) {
                $rows = array();
            }
            
    		$this->options["authors"] = $rows;
    		
        }
        
        return $this->options["authors"];
    }
    
    public function getQuotesOrdering() {
        
        return array(
            array("value"=>'0', "text"=> JText::_("LIB_VIPQUOTES_ORDERING")),
            array("value"=>'1', "text"=> JText::_("LIB_VIPQUOTES_ADDED_ASC")),
            array("value"=>'2', "text"=> JText::_("LIB_VIPQUOTES_ADDED_DESC")),
            array("value"=>'3', "text"=> JText::_("LIB_VIPQUOTES_AUTHOR_NAME")),
            array("value"=>'4', "text"=> JText::_("LIB_VIPQUOTES_POPULAR_QUOTES")),
            array("value"=>'5', "text"=> JText::_("LIB_VIPQUOTES_POPULAR_AUTHORS")),
        );
    }
    
    public function getAuthorsOrdering() {
        
        return array(
            array("value"=>'0', "text"=> JText::_("LIB_VIPQUOTES_ORDERING")),
            array("value"=>'1', "text"=> JText::_("LIB_VIPQUOTES_AUTHOR_NAME")),
            array("value"=>'2', "text"=> JText::_("LIB_VIPQUOTES_POPULAR_AUTHORS")),
        );
    }
}
