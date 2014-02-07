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

jimport('itprism.controller.form.backend');

/**
 * VipQuotes email controller class.
 *
 * @package		VipQuotes
 * @subpackage	Component
 * @since		1.6
 */
class VipQuotesControllerEmail extends ITPrismControllerFormBackend {
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $data    = $this->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id");
        
        $responseOptions = array(
            "task" => $this->getTask(),
            "id"   => $itemId
        );
        
        $model   = $this->getModel();
        /** @var $model VipQuotesModelEmail **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Validate the form data
        $validData = $model->validate($form, $data);
        
        // Check for errors
        if($validData === false){
            $this->displayNotice($form->getErrors(), $responseOptions);
            return;
        }
            
        try {
            
            $itemId = $model->save($validData);
            
            $responseOptions["id"] = $itemId;
            
        }catch(Exception $e){
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));
        }
        
        $this->displayMessage(JText::_('COM_VIPQUOTES_EMAIL_SAVED_SUCCESSFULLY'), $responseOptions);
    
    }
    
}