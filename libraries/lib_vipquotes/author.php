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
 * This class contains methods that are used for managing a author.
 *
 * @package      VipQuotes
 * @subpackage   Authors
 */
class VipQuotesAuthor
{
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

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver  $db Database Driver
     */
    public function __construct(JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Load a data about an author.
     *
     * <code>
     * $keys = array(
     *     "alias" => "steve-jobs"
     * );
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($keys);
     * </code>
     *
     * @param array  $keys
     */
    public function load($keys)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.name, a.alias, a.bio, a.image, a.thumb, a.copyright, a.hits, a.ordering, a.published")
            ->from($this->db->quoteName("#__vq_authors", "a"));

        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName("a." . $key) . "=" . $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }
    }

    /**
     * Set data to object properties.
     *
     * <code>
     * $ignored = array("id");
     *
     * $data = array(
     *      "id"  => 1,
     *     "name" => "Steve Jobs",
     *     "bio" => "..."
     * );
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->bind($data);
     * </code>
     *
     * @param array  $data
     * @param array  $ignored
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {

            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Return author ID.
     *
     * <code>
     * $keys = array(
     *     "alias" => "steve-jobs"
     * );
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($keys);
     *
     * if (!$author->getId()) {
     * ...
     * }
     * </code>
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set an author ID.
     *
     * <code>
     * $authorId = 1;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setId($authorId);
     * </code>
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Return author name.
     *
     * <code>
     * $keys = array(
     *     "alias" => "steve-jobs"
     * );
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($keys);
     *
     * $name = $author->getName();
     * </code>
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set an author name.
     *
     * <code>
     * $authorName = "John Dow";
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setName($authorName);
     * </code>
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Return author alias.
     *
     * <code>
     * $keys = array(
     *     "alias" => "steve-jobs"
     * );
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($keys);
     *
     * $alias = $author->getAlias();
     * </code>
     *
     * @return string $alias
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set an author alias.
     *
     * <code>
     * $authorAlias = "john-dow";
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setAlias($authorAlias);
     * </code>
     *
     * @param string $alias
     *
     * @return self
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Return author biography.
     *
     * <code>
     * $authorId = 1;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($authorId);
     *
     * $biography = $author->setBio();
     * </code>
     *
     * @return string $bio
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set an author biography.
     *
     * <code>
     * $authorBio = "..........";
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setBio($authorBio);
     * </code>
     *
     * @param string $bio
     *
     * @return self
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Return author image.
     *
     * <code>
     * $authorId = 1;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($authorId);
     *
     * $image = $author->getImage();
     * </code>
     *
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set an author image.
     *
     * <code>
     * $authorImage = "image_name.png";
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setImage($authorImage);
     * </code>
     *
     * @param string $image
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Return author thumbnail.
     *
     * <code>
     * $authorId = 1;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($authorId);
     *
     * $thumbnail = $author->getThumb();
     * </code>
     *
     * @return string $thumb
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * Set an author thumbnail.
     *
     * <code>
     * $authorThumbnail = "thumb_name.png";
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setThumb($authorThumbnail);
     * </code>
     *
     * @param string $thumb
     * @return self
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;

        return $this;
    }

    /**
     * Return information about image copyright.
     *
     * <code>
     * $authorId = 1;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($authorId);
     *
     * $copyright = $author->getCopyright();
     * </code>
     *
     * @return string $copyright
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * Set information about copyright of the image.
     *
     * <code>
     * $copyright = "....";
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setCopyright($copyright);
     * </code>
     *
     * @param string $copyright
     * @return self
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * Return a number of hits for an author page.
     *
     * <code>
     * $authorId = 1;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($authorId);
     *
     * $hits = $author->getHits();
     * </code>
     *
     * @return int $hits
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Set the number of hits over author page.
     *
     * <code>
     * $number = 10;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     *
     * $author->setHits($number);
     * </code>
     *
     * @param int $hits
     * @return self
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * Load a number of quotes for current author.
     *
     * <code>
     * $authorId = 1;
     *
     * $author = new VipQuotesAuthor(JFactory::getDbo());
     * $author->load($authorId);
     *
     * $numberOfQuotes = $author->getNumberOfQuotes();
     * </code>
     *
     * @return int  $result
     */
    public function getNumberOfQuotes()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__vq_quotes", "a"))
            ->where("a.author_id = " . (int)$this->id);

        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        if (!$result) {
            $result = 0;
        }

        return $result;
    }
}
