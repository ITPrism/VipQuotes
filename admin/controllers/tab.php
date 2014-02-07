<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.backend');

/**
 * Facebook Tab controller
 *
 * @package		ITPrism Components
 * @subpackage	VipQuotes
 * @since		1.6
 */
class VipQuotesControllerTab extends ITPrismControllerFormBackend {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Tab', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function save() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        // Gets the data from the form
        $data    = $this->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id", 0, "int");
        
        // Redirect options
        $redirectOptions = array (
            "task"	 => $this->getTask(),
            "id"     => $itemId
        );
        
        $model = $this->getModel();
        /** @var $model VipQuotesModelTab **/
        
        $form = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if (!$form) {
            throw new Exception($model->getError());
        }
        
        // Test for valid data.
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            $this->displayWarning($form->getErrors(), $redirectOptions);
            return;
        }
        
        // Check for installed tab in the system
        $pageId  = JArrayHelper::getValue($validData, "page_id", 0);
        $appId   = JArrayHelper::getValue($validData, "app_id");
        
        // If I want to add a new tab, but appID is used,
        // display error message.
        if(!$itemId AND $model->isInstalled($pageId, $appId)) {
            $this->displayWarning(JText::_("COM_VIPQUOTES_ERROR_FACEBOOK_APP_INSTALLED"), $redirectOptions);
            return;
        }
        
        try {
            
            // Get component parameters
            $params = JComponentHelper::getParams($this->option);
            
            $itemId = $model->save($validData);
            $item   = $model->getItem($itemId);
            
            if($validData["published"]) {
            
                // Install
                if(!$model->isInstalledFacebookTab($item, $params)) {
                    $model->installFacebookTab($item, $params);
                } else { // Update
                    $model->updateFacebookTab($item, $params);
                }
            
            } else {
                $model->uninstallFacebookTab($item, $params);
            }
            
            // Set item ID to redirect options
            $redirectOptions["id"] = $itemId;
            
        } catch ( Exception $e ) {
            $this->displayError(JText::_("COM_VIPQUOTES_ERROR_FACEBOOK"), array("view" => "pages"));
            return;
        }
        
        $this->displayMessage(JText::_('COM_VIPQUOTES_TAB_SAVED'), $redirectOptions);
        
    }
    
}