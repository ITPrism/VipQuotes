<?php
/**
* @package      Vip Quotes
* @subpackage   Libraries
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Vip Quotes is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_PLATFORM') or die;

/**
 * This class provieds functionality that manage quotes.
 */
class VipQuotesQuotes extends ArrayObject {
    
    protected $db;
    
    protected static $instances = array();
    
    /**
     * Load or set rewards. 
     * 
     * @param integer   $id      Project ID
     * @param array     $rewards Rewards
     */
    public function __construct($keys) {
        
        $this->db = JFactory::getDbo();
        
        $authorId   = JArrayHelper::getValue($keys, "author_id");
        $categoryId = JArrayHelper::getValue($keys, "catid");
        
        if(!empty($authorId)) {
            $items = $this->loadAuthorQuotes($authorId);
        } else if(!empty($categoryId)) {
            $items = $this->loadCategoryQuotes($categoryId);
        } else {
            $items = array();
        }
        
        parent::__construct($items);
    }

    public static function getInstance($keys)  {
    
        $authorId   = JArrayHelper::getValue($keys, "author_id");
        $categoryId = JArrayHelper::getValue($keys, "catid");
        
        if(!empty($authorId)) {
            $hash = md5("author_id:".$authorId);
        } else if(!empty($categoryId)) {
            $hash = md5("catid:".$authorId);
        } else {
            $hash = null;
        }

        if(is_null($hash)) {
            return null;
        }
        
        if (empty(self::$instances[$hash])){
            $item = new VipQuotesQuotes($keys);
            self::$instances[$hash] = $item;
        }
        
        return self::$instances[$hash];
    }
      
    
    public function loadAuthorQuotes($id) {
        
        $results = array();
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id")
            ->from($this->db->quoteName("#__vq_quotes") . " AS a")
            ->where("a.author_id = " .(int)$id);
        
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
        
        if(!$results) {
            $results = array();
        }
        
        return $results;
    }
    
    public function loadCategoryQuotes($id) {
    
        $results = array();
    
        $query = $this->db->getQuery(true);
    
        $query
        ->select("a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id")
        ->from($this->db->quoteName("#__vq_quotes") . " AS a")
        ->where("a.catid = " .(int)$id);
    
        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();
    
        if(!$results) {
            $results = array();
        }
    
        return $results;
    }
}
