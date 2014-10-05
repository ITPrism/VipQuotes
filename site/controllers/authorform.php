<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.frontend');

/**
 * Author Form controller class.
 *
 * @package        ITPrism Components
 * @subpackage     VipQuotes
 */
class VipQuotesControllerAuthorForm extends ITPrismControllerFormFrontend
{
    /**
     * Save an item.
     */
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

        $redirectOptions = array(
            "view" => "authorform",
        );

        $model = $this->getModel();
        /** @var $model VipQuotesModelAuthorForm */

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
        if ($params->get("security_check_authors")) {
            $name = JArrayHelper::getValue($data, "name");
            if ($model->hasDuplication($name)) {
                $this->displayWarning(JText::_('COM_VIPQUOTES_ERROR_AUTHOR_DUPLICATION'), $redirectOptions);
                return;
            }
        }

        try {

            // Get image
            $file = $this->input->files->get('jform', array(), 'array');
            $file = JArrayHelper::getValue($file, "image");

            // Upload image
            if (!empty($file['name'])) {

                $options = array(
                    "image_folder" => $params->get("images_directory", "images/authors"),
                    "image_width"  => JArrayHelper::getValue($validData, "image_width", 200),
                    "image_height" => JArrayHelper::getValue($validData, "image_height", 300),
                    "thumb_width"  => JArrayHelper::getValue($validData, "thumb_width", 50),
                    "thumb_height" => JArrayHelper::getValue($validData, "thumb_height", 50),
                );

                $imageNames = $model->uploadImage($file, $options);
                if (!empty($imageNames["image"])) {
                    $validData = array_merge($validData, $imageNames);
                }

            }

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

        $this->displayMessage(JText::_('COM_VIPQUOTES_AUTHOR_SAVED'), $redirectOptions);

    }
}
