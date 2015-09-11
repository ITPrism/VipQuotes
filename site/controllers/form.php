<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Quote controller class.
 *
 * @package        ITPrism Components
 * @subpackage     VipQuotes
 */
class VipQuotesControllerForm extends Prism\Controller\Form\Frontend
{
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Check for registered user
        $userId = JFactory::getUser()->get("id");
        if (!$userId) {
            $this->setMessage(JText::_('COM_VIPQUOTES_ERROR_NOT_LOG_IN'), "notice");
            $this->setRedirect(JRoute::_("index.php?option=com_users&view=login", false));
            return;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = JArrayHelper::getValue($data, "id");

        $redirectOptions = array(
            "view" => "form",
            "id"   => $itemId
        );

        $model = $this->getModel();
        /** @var $model VipQuotesModelForm */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_FORM_CANNOT_BE_LOADED"), 500);
        }

        // Test if the data is valid.
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        // Load component parameters.
        $params = $app->getParams($this->option);

        // Check for duplications.
        if ($params->get("security_check_quotes")) {
            $quote = JArrayHelper::getValue($data, "quote");
            if ($model->hasDuplication($quote, $itemId)) {
                $this->displayWarning(JText::_('COM_VIPQUOTES_ERROR_DUPLICATION'), $redirectOptions);

                return;
            }
        }

        // Set states
        $authorId   = JArrayHelper::getValue($validData, "author_id");
        $categoryId = JArrayHelper::getValue($validData, "catid");

        $app->setUserState($this->option . ".edit.quote.author_id", $authorId);
        $app->setUserState($this->option . ".edit.quote.category_id", $categoryId);

        try {

            // Verify for enabled magic quotes.
            if (get_magic_quotes_gpc()) {
                $validData["quote"] = stripslashes($validData["quote"]);
            }

            // Save quote
            $itemId = $model->save($validData, $params);

            $redirectOptions["id"] = $itemId;

        } catch (RuntimeException $e) {
            $this->displayWarning($e->getMessage(), $redirectOptions);

            return;
        } catch (Exception $e) {
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'), 500);
        }

        // Reset the ID and redirect to form to be added a new item.
        $task = $this->getTask();
        if (strcmp("save2new", $task) == 0) {
            unset($redirectOptions["id"]);
        }

        $this->displayMessage(JText::_('COM_VIPQUOTES_QUOTE_SAVED'), $redirectOptions);
    }
}
