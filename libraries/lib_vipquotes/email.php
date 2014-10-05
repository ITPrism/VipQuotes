<?php
/**
 * @package      VipQuotes
 * @subpackage   Emails
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing e-mail templates.
 * 
 * @package      VipQuotes
 * @subpackage   Emails
 */
class VipQuotesEmail
{
    protected $id;
    protected $subject;
    protected $body;
    protected $senderName;
    protected $senderEmail;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    protected $replaceable = array(
        "{SITE_NAME}",
        "{SITE_URL}",
        "{ITEM_TITLE}",
        "{ITEM_URL}",
        "{SENDER_NAME}",
        "{SENDER_EMAIL}",
        "{RECIPIENT_NAME}",
        "{RECIPIENT_EMAIL}",
        "{CATEGORY_NAME}",
        "{CATEGORY_URL}",
        "{AUTHOR_NAME}",
        "{AUTHOR_URL}",
    );

    /**
     * Initialize the object.
     *
     * <code>
     * $subject  = "My e-mail subject...";
     * $body     = "My e-mail body...";
     * $email    = new VipQuotesEmail($subject, $body);
     * </code>
     *
     * @param string $subject
     * @param string $body
     */
    public function __construct($subject = "", $body = "")
    {
        $this->subject = $subject;
        $this->body    = $body;
    }

    /**
     * Set the database object.
     *
     * <code>
     * $email    = new VipQuotesEmail();
     * $email->setDb(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Load an email data from database.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VipQuotesEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     * </code>
     *
     * @param int $id  The ID of the e-mail template.
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.subject, a.body, a.sender_name, a.sender_email")
            ->from($this->db->quoteName("#__vq_emails", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Return email ID.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * if (!$email->getId()) {
     * ....
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set data to object properties.
     *
     * <code>
     * $data = array(
     *  "subject" => "My e-mail subject...",
     *  "body" =>"My e-mail body..."
     * );
     *
     * $email    = new VipQuotesEmail();
     * $email->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
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
     * Set email subject.
     *
     * <code>
     * $emailId  = 1;
     * $subject  = "My subject...";
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $email->setSubject($subject)
     * </code>
     *
     * @param string $subject
     *
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = strip_tags($subject);

        return $this;
    }

    /**
     * Return email title.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $subject = $email->getSubject()
     * </code>
     *
     * @return string
     */
    public function getSubject()
    {
        return strip_tags($this->subject);
    }

    /**
     * Set email body.
     *
     * <code>
     * $emailId  = 1;
     * $body  = "My body...";
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $email->setBody($body)
     * </code>
     *
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Return body of the message.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VipQuotesEmail();
     * $email->setDb(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $body    = $item->getBody("plain");
     * </code>
     *
     * @param string $mode Mail type - html or plain ( plain text ).
     *
     * @return string
     */
    public function getBody($mode = "html")
    {
        $mode = intval($mode);

        if (strcmp("plain", $mode) == 0) { // Plain text
            $body = str_replace("<br />", "\n", $this->body);
            $body = strip_tags($body);

            return $body;
        } else { // HTML text.
            return $this->body;
        }
    }

    /**
     * Set the name of the sender.
     *
     * <code>
     * $emailId  = 1;
     * $name     = "John Dow";
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $email->setSenderName($name)
     * </code>
     *
     * @param string $name
     *
     * @return self
     */
    public function setSenderName($name)
    {
        $this->senderName = $name;

        return $this;
    }

    /**
     * Return the name of the sender.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $name = $email->getSenderName()
     * </code>
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set the name of the sender.
     *
     * <code>
     * $emailId  = 1;
     * $email    = "john@gmail.com";
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * $email->setSenderEmail($email)
     * </code>
     *
     * @param string $email
     *
     * @return self
     */
    public function setSenderEmail($email)
    {
        $this->senderEmail = $email;

        return $this;
    }

    /**
     * Return the email of the sender.
     *
     * <code>
     * $emailId  = 1;
     *
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     *
     * echo $email->getSenderEmail()
     * </code>
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Parse subject and body, replacing indicators with other values.
     *
     * <code>
     * $subject = "Here you are my website.";
     * $body = "My website is {WEBSITE}...";
     *
     * $data = array(
     *  "website" => "http://itprism.com"
     * );
     *
     * $email    = new VipQuotesEmail($subject, $body);
     * $email->bind($data);
     *
     * // Replace {WEBSITE} with http://itprism.com.
     * $email->parse($data);
     *
     * $body = $email->setBody();
     * </code>
     *
     * @param array $data
     *
     * @return string
     */
    public function parse($data)
    {
        foreach ($data as $key => $value) {

            // Prepare flag
            $search = "{" . JString::strtoupper($key) . "}";

            // Parse subject
            $this->subject = str_replace($search, $value, $this->subject);

            // Parse body
            $this->body = str_replace($search, $value, $this->body);

        }

        return $this;
    }
}
