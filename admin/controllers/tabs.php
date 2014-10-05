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

jimport('itprism.controller.admin');

/**
 * VipQuotes Facebook Pages Controller
 *
 * @package     VipQuotes
 * @subpackage  VipQuotes
 */
class VipQuotesControllerTabs extends ITPrismControllerAdmin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Tab', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function delete()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $redirectOptions = array(
            "view" => $this->view_list
        );

        $cid = $this->input->get('cid', array(), 'array');
        JArrayHelper::toInteger($cid);

        if (empty($cid)) {
            $this->displayNotice(JText::_($this->text_prefix . "_ERROR_FACEBOOK_INVALID_TAB"), $redirectOptions);

            return;
        }

        $model = $this->getModel();
        /** @var $model VipQuotesModelTab */

        // Get component parameters
        $params = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        try {

            foreach ($cid as $itemId) {
                $item = $model->getItem($itemId);
                $model->uninstallFacebookTab($item, $params);
                $model->delete($item->id);
            }

        } catch (Exception $e) {
            $this->displayError(JText::_($this->text_prefix . "_ERROR_FACEBOOK"), array("view" => "pages"));

            return;
        }

        $msg = JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid));
        $this->displayMessage($msg, $redirectOptions);
    }

    public function publish()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $redirectOptions = array(
            "view" => $this->view_list
        );

        $cid = $this->input->get('cid', array(), 'array');
        JArrayHelper::toInteger($cid);

        if (empty($cid)) {
            $this->displayNotice(JText::_($this->text_prefix . "_ERROR_FACEBOOK_INVALID_TAB"), $redirectOptions);

            return;
        }

        $data   = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task   = $this->getTask();
        $value  = JArrayHelper::getValue($data, $task, 0, 'int');

        $model = $this->getModel();
        /** @var $model VipQuotesModelTab * */

        // Get component parameters
        $params = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        try {

            foreach ($cid as $itemId) {

                $item = $model->getItem($itemId);

                if (!$value) {
                    $model->uninstallFacebookTab($item, $params);
                } else {
                    $model->installFacebookTab($item, $params);
                }

            }

            // Publish or not
            $model->publish($cid, $value);

        } catch (Exception $e) {
            $this->displayError(JText::_($this->text_prefix . "_ERROR_FACEBOOK"), array("view" => "pages"));

            return;
        }

        // Set message
        if (!$value) {
            $msg = JText::plural($this->text_prefix . '_N_ITEMS_UNPUBLISHED', count($cid));
        } else {
            $msg = JText::plural($this->text_prefix . '_N_ITEMS_PUBLISHED', count($cid));
        }

        $this->displayMessage($msg, $redirectOptions);
    }
}
