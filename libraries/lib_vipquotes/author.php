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
 * This class contains methods that are used for managing a author.
 *
 * @package      VipQuotes
 * @subpackage   Libraries
 */
class VipQuotesAuthor {
    
    protected $id;
    protected $name;
    protected $alias;
    protected $bio;
    protected $image;
    protected $thumb;
    protected $copyright;
    protected $hits;
    protected $ordering;
    protected $published;
    
    protected $db;
    
    public function __construct(JDatabase $db) {
        $this->db = $db;
    }
    
    public function load($keys) {
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.name, a.alias, a.bio, a.image, a.thumb, a.copyright, a.hits, a.ordering, a.published")
            ->from($this->db->quoteName("#__vq_authors", "a"));
        
        if(is_array($keys)) {
            foreach($keys as $key => $value) {
                $query->where($this->db->quoteName("a.".$key) ."=".$this->db->quote($value));
            }
        } else {
            $query->where("a.id = " .(int)$keys);
        }
        
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
     * @param field_type $id
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

	/**
     * @return the $name
     */
    public function getName() {
        return $this->name;
    }

	/**
     * @param field_type $name
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

	/**
     * @return the $alias
     */
    public function getAlias() {
        return $this->alias;
    }

	/**
     * @param field_type $alias
     */
    public function setAlias($alias) {
        $this->alias = $alias;
        return $this;
    }

	/**
     * @return the $bio
     */
    public function getBio() {
        return $this->bio;
    }

	/**
     * @param field_type $bio
     */
    public function setBio($bio) {
        $this->bio = $bio;
        return $this;
    }

	/**
     * @return the $image
     */
    public function getImage() {
        return $this->image;
    }

	/**
     * @param field_type $image
     */
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

	/**
     * @return the $thumb
     */
    public function getThumb() {
        return $this->thumb;
    }

	/**
     * @param field_type $thumb
     */
    public function setThumb($thumb) {
        $this->thumb = $thumb;
        return $this;
    }

	/**
     * @return the $copyright
     */
    public function getCopyright() {
        return $this->copyright;
    }

	/**
     * @param field_type $copyright
     */
    public function setCopyright($copyright) {
        $this->copyright = $copyright;
        return $this;
    }

	/**
     * @return the $hits
     */
    public function getHits() {
        return $this->hits;
    }

	/**
     * @param field_type $hits
     */
    public function setHits($hits) {
        $this->hits = $hits;
        return $this;
    }

    public function getNumberOfQuotes() {
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        
        $query
            ->select("COUNT(*)")
            ->from( $this->db->quoteName("#__vq_quotes", "a"))
            ->where("a.author_id = " .(int)$this->id);
         
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
         
        if(!$result) {
            $result = 0;
        }
        
        return $result;
        
    }
    
    
}
