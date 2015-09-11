<?php
/**
 * @package         VipQuotes
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('Prism.init');
jimport('EmailTemplates.init');
jimport('VipQuotes.init');

/**
 * This plugin send notification mails to the administrator.
 *
 * @package        VipQuotes
 * @subpackage     Plugins
 */
class plgContentVipQuotesAdminMail extends JPlugin
{
    /**
     * A JRegistry object holding the parameters for the plugin
     *
     * @var    Joomla\Registry\Registry
     * @since  1.5
     */
    public $params = null;

    protected $autoloadLanguage = true;

    /**
     * This method is executed when someone post a quote.
     *
     * @param string    $context
     * @param object    $item
     * @param boolean   $isNew
     *
     * @return null|boolean
     */
    public function onContentAfterSave($context, $item, $isNew)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        if (strcmp("com_vipquotes.quote", $context) != 0) {
            return null;
        }

        // Check for enabled option for sending mail
        // when user post a quote.
        $emailId = $this->params->get("send_when_post", 0);
        if (!$emailId) {
            return true;
        }

        if (!empty($item->id) and $isNew) {

            // Send email to the administrator.
            $return = $this->sendMails($item, $emailId);

            // Check for error.
            if ($return !== true) {
                Jlog::add(JText::_("PLG_CONTENT_VIPQUOTESADMINMAIL_ERROR_MAIL_SENDING_USER"));

                return false;
            }

        }

        return true;
    }

    protected function sendMails($item, $emailId)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $result = false;

        // Send mail to the administrator
        if (!empty($emailId)) {

            // Get website
            $uri     = JUri::getInstance();
            $website = $uri->toString(array("scheme", "host"));

            $emailMode = $this->params->get("email_mode", 0);

            $quote = new VipQuotes\Quote\Quote(JFactory::getDbo());
            $quote->load($item->id);

            $subject = JText::sprintf("PLG_CONTENT_VIPQUOTESADMINMAIL_DEFAULT_SUBJECT", $quote->getAuthor(), $quote->getCategory());

            // Prepare default data that will be parsed.
            $data = array(
                "site_name"     => $app->get("sitename"),
                "site_url"      => JUri::root(),
                "item_title"    => $subject,
                "item_url"      => $website . JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid)),
                "category_name" => $quote->getCategory(),
                "category_url"  => $website . JRoute::_(VipQuotesHelperRoute::getCategoryRoute($quote->getCategorySlug())),
                "author_name"   => $quote->getAuthor(),
                "author_url"    => $website . JRoute::_(VipQuotesHelperRoute::getAuthorRoute($quote->getAuthorSlug())),
            );

            // Get the e-mail.
            $email = new EmailTemplates\Email();
            $email->setDb(JFactory::getDbo());
            $email->load($emailId);

            // Check for valid predefined e-mail.
            if (!$email->getId()) {
                return false;
            }

            if (!$email->getSenderName()) {
                $email->setSenderName($app->get("fromname"));
            }
            if (!$email->getSenderEmail()) {
                $email->setSenderEmail($app->get("mailfrom"));
            }

            $recipientName = $email->getSenderName();
            $recipientMail = $email->getSenderEmail();

            // Prepare data for parsing
            $data["sender_name"]     = $email->getSenderName();
            $data["sender_email"]    = $email->getSenderEmail();
            $data["recipient_name"]  = $recipientName;
            $data["recipient_email"] = $recipientMail;

            $email->parse($data);
            $subject = $email->getSubject();

            $mailer = JFactory::getMailer();
            if (strcmp("html", $emailMode) == 0) { // Send as HTML message

                $body   = $email->getBody(Prism\Constants::MAIL_MODE_HTML);
                $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_HTML);

            } else { // Send as plain text.

                $body   = $email->getBody(Prism\Constants::MAIL_MODE_PLAIN);
                $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_PLAIN);

            }
        }

        return ($result !== true) ? false : true;
    }
}
