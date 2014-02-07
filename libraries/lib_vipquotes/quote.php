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
 * This class contains methods that are used for managing a quote.
 *
 * @package      VipQuotes
 * @subpackage   Libraries
 */
class VipQuotesQuote {
    
    protected $id;
    protected $quote;
    protected $created;
    protected $published;
    protected $ordering;
    protected $hits;
    protected $author_id;
    protected $catid;
    protected $user_id;
    
    protected $catslug;
    protected $authorslug;
    
    protected $category;
    protected $author;
    
    protected $db;
    
    public function __construct(JDatabase $db) {
        $this->db = $db;
    }
    
    public function load($id) {
        
        if(!$id){
            throw new InvalidArgumentException(JText::_("LIB_VIPQUOTES_ERROR_INVALID_PARAMETER_ID"));
        }
        
        $query = $this->db->getQuery(true);
        
        $query->select(
                "a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id, " .
                "b.name AS author, b.thumb AS author_thumb, b.image AS author_image, " .
                "c.title AS category");
        $query->select($query->concatenate(array("b.id", "b.alias"), "-") . " AS authorslug");
        $query->select($query->concatenate(array("c.id", "c.alias"), "-") . " AS catslug");
        
        $query->from($this->db->quoteName("#__vq_quotes", "a"));
        $query->leftJoin($this->db->quoteName("#__vq_authors", "b") ." ON a.author_id = b.id");
        $query->leftJoin($this->db->quoteName("#__categories", "c") ." ON a.catid = c.id");
        
        $query->where("a.id = " .(int)$id);
        
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!empty($result)) {
            $this->bind($result);
        }
        
    }
    
    public function bind($data, $ignore = array()) {
        
        foreach($data as $key => $value) {
            
            if(!in_array($key, $ignore)) {
                $this->$key = $value;
            }            
            
        }
    }
    
	/**
     * @return the $id
     */
    public function getId() {
        return $this->id;
    }

	/**
     * @return the $quote
     */
    public function getQuote() {
        return $this->quote;
    }

	/**
     * @return the $created
     */
    public function getCreated() {
        return $this->created;
    }

	/**
     * @return the $published
     */
    public function getPublished() {
        return $this->published;
    }

	/**
     * @return the $ordering
     */
    public function getOrdering() {
        return $this->ordering;
    }

	/**
     * @return the $hits
     */
    public function getHits() {
        return $this->hits;
    }

	/**
     * @return the $author_id
     */
    public function getAuthorId() {
        return $this->author_id;
    }

	/**
     * @return the $catid
     */
    public function getCatid() {
        return $this->catid;
    }

	/**
     * @return the $user_id
     */
    public function getUserId() {
        return $this->user_id;
    }

	/**
     * @return the $catslug
     */
    public function getCategorySlug() {
        return $this->catslug;
    }

	/**
     * @return the $authorslug
     */
    public function getAuthorSlug() {
        return $this->authorslug;
    }
    
	/**
     * @return the $category
     */
    public function getCategory() {
        return $this->category;
    }

	/**
     * @return the $author
     */
    public function getAuthor() {
        return $this->author;
    }

    public function getProperties() {
        
        $vars = get_object_vars($this);
        unset($vars["db"]);
    
        return $vars;
    }
}
