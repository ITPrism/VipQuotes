<?php
/**
 * @package		 VipQuotes
 * @subpackage	 Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('vipquotes.init');

/**
 * This plugin send notification mails to a user. 
 *
 * @package		VipQuotes
 * @subpackage	Plugins
 */
class plgContentVipQuotesUserMail extends JPlugin {
    
    protected $autoloadLanguage = true;
    
    /**
     * This method is executed when the administrator change the state of a quote.
     * 
     * @param string            $context
     * @param array             $ids
     * @param integer           $state
     * 
     * @return boolean
     */
    public function onContentChangeState($context, $ids, $state) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if(!$app->isAdmin()) {
            return;
        }

        if(strcmp("com_vipquotes.quote", $context) != 0){
            return;
        }
        
        // Check for enabled option for sending mail 
        // when the administrator publish a quote.
        $emailId = $this->params->get("send_when_publish", 0);
        if(!$emailId) {
            return;
        }
        
        if(!empty($ids) AND $state == VipQuotesConstants::PUBLISHED) {
        
            jimport("vipquotes.quotes");
            $items = new VipQuotesQuotes(JFactory::getDbo());
            $items->load($ids);
        
            if(count($items) <= 0) {
                return true;
            }
        
            // Load class VipQuotesEmail.
            jimport("vipquotes.quote");
            jimport("vipquotes.email");
            
            // Get the e-mail.
            $email    = new VipQuotesEmail();
            $email->setDb(JFactory::getDbo());
            $email->load($emailId);
            
            // Check for valid predefined e-mail.
            if(!$email->getId()) {
                return false;
            }
        
            if(!$email->getSenderName()) {
                $email->setSenderName($app->getCfg("fromname"));
            }
            if(!$email->getSenderEmail()) {
                $email->setSenderEmail($app->getCfg("mailfrom"));
            }
            
            foreach($items as $item) {
        
                $quoteData  = JArrayHelper::fromObject($item);
                
                $quote      = new VipQuotesQuote(JFactory::getDbo());
                $quote->bind($quoteData);
                
                // Send email to the administrator.
                $return = $this->sendMails($quote, $email);
        
                // Check for error.
                if ($return !== true) {
                    Jlog::add(JText::_("PLG_CONTENT_VIPQUOTESUSERMAIL_ERROR_MAIL_SENDING_USER"));
                    return false;
                }
        
            }
        
        }
        
        return true;
        
    }

    public function onContentAfterSave($context, $item, $isNew, $isChangedState) {
    
        $app = JFactory::getApplication();
        /** @var $app JSite **/
    
        if(!$app->isAdmin()) {
            return;
        }
    
        if(strcmp("com_vipquotes.quote", $context) != 0){
            return;
        }
    
        // Check for enabled option for sending mail
        // when user post a quote.
        $emailId = $this->params->get("send_when_publish", 0);
        if(!$emailId) {
            return;
        }
    
        if(!empty($item->id) AND $isChangedState) {
    
            // Load class VipQuotesEmail.
            jimport("vipquotes.quote");
            jimport("vipquotes.email");
            
            // Get the e-mail.
            $email    = new VipQuotesEmail();
            $email->setDb(JFactory::getDbo());
            $email->load($emailId);
            
            // Check for valid predefined e-mail.
            if(!$email->getId()) {
                return false;
            }
        
            if(!$email->getSenderName()) {
                $email->setSenderName($app->getCfg("fromname"));
            }
            if(!$email->getSenderEmail()) {
                $email->setSenderEmail($app->getCfg("mailfrom"));
            }
    
            // Get quote
            $quote      = new VipQuotesQuote(JFactory::getDbo());
            $quote->load($item->id);
            
            // Send email to the administrator.
            $return = $this->sendMails($quote, $email);
    
            // Check for error.
            if ($return !== true) {
                Jlog::add(JText::_("PLG_CONTENT_VIPQUOTESADMINMAIL_ERROR_MAIL_SENDING_USER"));
                return false;
            }
    
        }
    
        return true;
    
    }
    
    protected function sendMails($quote, $email) {
    
        $app = JFactory::getApplication();
        /** @var $app JSite **/
    
        $result = false;
        
        // Send mail to the user
            
        // Get website
        $uri        = JUri::getInstance();
        $website    = $uri->toString(array("scheme", "host"));
        
        $emailMode  = $this->params->get("email_mode", 0);
        
        $subject    = JText::sprintf("PLG_CONTENT_VIPQUOTESUSERMAIL_DEFAULT_SUBJECT", $app->getCfg("sitename"));
        
        // Prepare default data that will be parsed.
        $data = array(
            "site_name"         => $app->getCfg("sitename"),
            "site_url"          => JUri::root(),
            "item_title"        => $subject,
            "item_url"          => $website."/".VipQuotesHelperRoute::getQuoteRoute($quote->getId(), $quote->getCatId()),
            "category_name"     => $quote->getCategory(),
            "category_url"      => $website."/".VipQuotesHelperRoute::getCategoryRoute($quote->getCategorySlug()),
            "author_name"       => $quote->getAuthor(),
            "author_url"        => $website."/".VipQuotesHelperRoute::getAuthorRoute($quote->getAuthorSlug()),
        );
        
        $user = JFactory::getUser($quote->getUserId());
        if(!$user->id) {
            return false;
        }
        
        // Prepare data for parsing
        $data["sender_name"]     =  $email->getSenderName();
        $data["sender_email"]    =  $email->getSenderEmail();
        $data["recipient_name"]  =  $user->name;
        $data["recipient_email"] =  $user->email;

        $email->parse($data);
        $subject    = $email->getSubject();

        $mailer  = JFactory::getMailer();
        if(strcmp("html", $emailMode) == 0) { // Send as HTML message
            
            $body    = $email->getBody(VipQuotesEmail::MAIL_MODE_HTML);
            $result  = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $user->email, $subject, $body, VipQuotesEmail::MAIL_MODE_HTML);

        } else { // Send as plain text.
            
            $body    = $email->getBody(VipQuotesEmail::MAIL_MODE_PLAIN);
            $result  = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $user->email, $subject, $body, VipQuotesEmail::MAIL_MODE_PLAIN);

        }
            
        return ($result !== true) ? false : true;
    }
}