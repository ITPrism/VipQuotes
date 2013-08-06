<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipQuotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
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
 * Quote controller class.
 *
 * @package		ITPrism Components
 * @subpackage	VipQuotes
 */
class VipQuotesControllerQuote extends ITPrismControllerFormBackend {
    
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Quote', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id");
        
        $redirectOptions = array(
            "task" => $this->getTask(),
            "id"   => $itemId
        );
        
        $model   = $this->getModel();
        /** @var $model VipQuotesModelQuote **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError());
        }
            
        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }
       
        // Load component parameters.
        $params  = JComponentHelper::getParams('com_vipquotes');
        
        // Check for duplications
        if($params->get("quotes_check_quotes")) {
            $quote = JArrayHelper::getValue($data, "quote");
            
            if($model->hasDuplication($quote, $itemId)) {
                
                $redirectOptions = array(
                    "view" => "quotes",
                );
                
                $this->displayNotice(JText::_('COM_VIPQUOTES_ERROR_DUPLICATION'), $redirectOptions);
                return;
            }
        }
            
        try{
            
            // Verify for enabled magic quotes
            if( get_magic_quotes_gpc() ) {
                $validData["quote"] = stripslashes($validData["quote"]);
            }
            
            $itemId = $model->save($validData);
            
            $redirectOptions["id"] = $itemId;
            
        } catch(Exception $e){
            JLog::add($e->getMessage());
            throw new Exception( JText::_('COM_VIPQUOTES_ERROR_SYSTEM'), ITPrismErrors::CODE_ERROR);
        }
        
        $this->displayMessage(JText::_('COM_VIPQUOTES_QUOTE_SAVED'), $redirectOptions);
    
    }
    
}