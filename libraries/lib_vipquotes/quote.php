<?php
/**
 * @package      VipQuotes
 * @subpackage   Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a quote.
 *
 * @package      VipQuotes
 * @subpackage   Quotes
 */
class VipQuotesQuote
{
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

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Load data about quote from database.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     * </code>
     *
     * @param int $id
     *
     * @throws InvalidArgumentException
     */
    public function load($id)
    {
        if (!$id) {
            throw new InvalidArgumentException(JText::_("LIB_VIPQUOTES_ERROR_INVALID_PARAMETER_ID"));
        }

        $query = $this->db->getQuery(true);

        $query->select(
            "a.id, a.quote, a.created, a.published, a.ordering, a.hits, a.author_id, a.catid, a.user_id, " .
            "b.name AS author, b.thumb AS author_thumb, b.image AS author_image, " .
            "c.title AS category"
        );
        $query->select($query->concatenate(array("b.id", "b.alias"), "-") . " AS authorslug");
        $query->select($query->concatenate(array("c.id", "c.alias"), "-") . " AS catslug");

        $query->from($this->db->quoteName("#__vq_quotes", "a"));
        $query->leftJoin($this->db->quoteName("#__vq_authors", "b") . " ON a.author_id = b.id");
        $query->leftJoin($this->db->quoteName("#__categories", "c") . " ON a.catid = c.id");

        $query->where("a.id = " . (int)$id);

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
     *     "id"  => 1,
     *     "quote" => "..."
     * );
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->bind($data);
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
     * Return a quote ID.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * if (!$quote->getId()) {
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
     * Return a quote.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getQuote();
     * </code>
     *
     * @return string $quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Return the date when the quote has been added.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getCreated();
     * </code>
     *
     * @return string $created
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Return the flag that show you if the quote is published or not.
     * 1 = published; 0 = not published;
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * $isPublished = $quote->getPublished();
     * </code>
     *
     * @return int $published
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Return the number of the quote in the list.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getOrdering();
     * </code>
     *
     * @return int $ordering
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Return a number of hits on quote page.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getHits();
     * </code>
     *
     * @return int $hits
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Return an author ID.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getAuthorId();
     * </code>
     *
     * @return int $author_id
     */
    public function getAuthorId()
    {
        return $this->author_id;
    }

    /**
     * Return a category ID where the quote is.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getCatid();
     * </code>
     *
     * @return int $catid
     */
    public function getCatid()
    {
        return $this->catid;
    }

    /**
     * Return a user ID.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getUserId();
     * </code>
     *
     * @return int $user_id
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Return a category slug where the quote is.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getCategorySlug();
     * </code>
     *
     * @return string $catslug
     */
    public function getCategorySlug()
    {
        return $this->catslug;
    }

    /**
     * Return an author slug.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getAuthorSlug();
     * </code>
     *
     * @return string $authorslug
     */
    public function getAuthorSlug()
    {
        return $this->authorslug;
    }

    /**
     * Return a name of a category.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getCategory();
     * </code>
     *
     * @return string $category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Return a name of an author.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * echo $quote->getCategory();
     * </code>
     *
     * @return string $author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Return object properties as array.
     *
     * <code>
     * $id = 1;
     *
     * $quote = new VipQuotesQuote(JFactory::getDbo());
     * $quote->load($id);
     *
     * $properties = $quote->getProperties();
     * </code>
     *
     * @return string $author
     */
    public function getProperties()
    {
        $vars = get_object_vars($this);
        unset($vars["db"]);

        return $vars;
    }
}
