<?php
/**
 * @package      VipQuotes
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

class VipQuotesEmail {
    
    const MAIL_MODE_HTML    = 1;
    const MAIL_MODE_PLAIN   = 0;
    
    protected $id;
    protected $subject;
    protected $body;
    protected $senderName;
    protected $senderEmail;
    
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

    public function __construct($subject = "", $body = "") {
        $this->subject = $subject;
        $this->body    = $body;
    }
    
    public function setDb($db) {
        $this->db = $db;
        return $this;
    }
    /**
     * Load an email data from database.
     *
     * @param $keys ID or Array with IDs
     * @param $reset Reset the record values.
     * 
     * @return self
     *
     * <code>
     *
     * $emailId  = 1;
     * $email    = new VipQuotesEmail();
     * $email->load($emailId);
     * 
     * </code>
     */
    public function load($id) {
        
        $query  = $this->db->getQuery(true);
        
        $query
            ->select("a.id, a.subject, a.body, a.sender_name, a.sender_email")
            ->from($this->db->quoteName("#__vq_emails", "a"))
            ->where("a.id = " .(int)$id);
        
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();
        
        if(!$result) {
            $result = array();
        }
        
        $this->bind($result);
        
        return $this;
    }

    public function bind($data) {
        
        $this->setSubject(JArrayHelper::getValue($data, "subject"));
        $this->setBody(JArrayHelper::getValue($data, "body"));
        $this->setSenderName(JArrayHelper::getValue($data, "sender_name"));
        $this->setSenderEmail(JArrayHelper::getValue($data, "sender_email"));
        
        $this->id = JArrayHelper::getValue($data, "id");
        
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = strip_tags($subject);
        return $this;
    }
    
    public function getSubject() {
        return strip_tags($this->subject);
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }
    
    /**
     * Return body of the message.
     * 
     * @param string Mail type - html or plain ( plain text ).
     * 
     * @return string
     * 
     * <code>
     * 
     * $emailId  = 1;
     * $email    = new VipQuotesEmail(JFactory::getDbo());
     * $email->load($emailId);
     * 
     * $body     = $item->getBody(VipQuotesEmail::MAIL_MODE_PLAIN);
     * 
     * </code>
     */
    public function getBody($mode = 0) {
        
        $mode = intval($mode);
        
        if($mode === VipQuotesEmail::MAIL_MODE_PLAIN) { // Plain text
            $body = str_replace("<br />", "\n", $this->body);
            $body = strip_tags($body);
            
            return $body;
            
        } else { // HTML text.
            
            return $this->body;
            
        }
        
    }

    public function setSenderName($name) {
        $this->senderName = $name;
        return $this;
    }

    public function getSenderName() {
        return $this->senderName;
    }

    public function setSenderEmail($email) {
        $this->senderEmail = $email;
        return $this;
    }

    public function getSenderEmail() {
        return $this->senderEmail;
    }
    
    public function parse($data) {
        
        foreach($data as $key => $value) {
            
            // Prepare flag
            $search = "{".JString::strtoupper($key)."}";
            
            // Parse subject
            $this->subject = str_replace($search, $value, $this->subject);
            
            // Parse body
            $this->body = str_replace($search, $value, $this->body);
            
        }
        
        return $this;
    }
    
    public function getId(){
        return $this->id;
    }
}