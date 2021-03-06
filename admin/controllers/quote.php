<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
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
class VipQuotesControllerQuote extends Prism\Controller\Form\Backend
{
    public function getModel($name = 'Quote', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
    
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, "id");

        $redirectOptions = array(
            "task" => $this->getTask(),
            "id"   => $itemId
        );

        $model = $this->getModel();
        /** @var $model VipQuotesModelQuote */

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
        $params = JComponentHelper::getParams('com_vipquotes');
        /** @var  $params Joomla\Registry\Registry */

        // Check for duplications
        if ($params->get("quotes_check_quotes")) {

            $quote = Joomla\Utilities\ArrayHelper::getValue($data, "quote");

            if ($model->hasDuplication($quote, $itemId)) {

                $redirectOptions = array(
                    "view" => "quotes",
                );

                $this->displayNotice(JText::_('COM_VIPQUOTES_ERROR_DUPLICATION'), $redirectOptions);

                return;
            }
        }

        try {

            // Verify for enabled magic quotes
            if (get_magic_quotes_gpc()) {
                $validData["quote"] = stripslashes($validData["quote"]);
            }

            $itemId = $model->save($validData);

            $redirectOptions["id"] = $itemId;

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_VIPQUOTES_QUOTE_SAVED'), $redirectOptions);

    }
}
