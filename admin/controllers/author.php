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

class VipQuotesControllerAuthor extends ITPrismControllerFormBackend {
    
	/**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Author', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        
        // Load parameters.
        $params                 = JComponentHelper::getParams($this->option);
        $model->imagesFolder    = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $params->get("images_directory", "images/authors"));

        return $model;
    }
    
    /**
     * Save an item
     */
    public function save(){
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $data    = $this->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id");
        
        // Redirect options
        $redirectOptions = array (
            "task"	 => $this->getTask(),
            "id"     => $itemId
        );
        
        $model   = $this->getModel();
        /** @var $model VipQuotesModelAuthor **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError());
        }
            
        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            $this->displayWarning($form->getErrors(), $redirectOptions);
            return;
        }
       
        try {
            
            // Get image
            $file   = $this->input->files->get('jform', array(), 'array');
            $file   = JArrayHelper::getValue($file, "image");
            
            // Upload image
            if(!empty($file['name'])) {
            
                $options = array(
                    "resize_image"  => JArrayHelper::getValue($validData, "resize_image", 0),
                    "image_width"   => JArrayHelper::getValue($validData, "image_width",  200),
                    "image_height"  => JArrayHelper::getValue($validData, "image_height", 300),
                    "thumb_width"   => JArrayHelper::getValue($validData, "thumb_width",  50),
                    "thumb_height"  => JArrayHelper::getValue($validData, "thumb_height", 50),
                );
                
                $imageNames    = $model->uploadImage($file, $options);
                if(!empty($imageNames["image"])) {
                    $validData = array_merge($validData, $imageNames);
                }
            
            }
            
            // Verify for enabled magic quotes
            if( get_magic_quotes_gpc() ) {
                $validData["name"] = stripcslashes($validData["name"]);
                $validData["bio"]  = stripcslashes($validData["bio"]);
            }
            
            $itemId = $model->save($validData);
            
            $redirectOptions["id"] = $itemId;
            
        } catch(RuntimeException $e){
            $this->displayWarning($e->getMessage(), $redirectOptions);
            return;
        } catch(Exception $e){
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));
            
        }
        
        $this->displayMessage(JText::_('COM_VIPQUOTES_AUTHOR_SAVED'), $redirectOptions);
    
    }
    
	/**
     * Delete image
     */
    public function removeImage() {
        
        $itemId = $this->input->getInt('id');
        if(!$itemId){
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_IMAGE_DOES_NOT_EXIST'));
        }
        
        // Redirect options
        $redirectOptions = array (
            "view"	 => "author",
            "id"     => $itemId
        );
        
        try {
            
            $model = $this->getModel();
            $model->removeImage($itemId);
            
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));
        }
        
        $this->displayMessage(JText::_('COM_VIPQUOTES_IMAGE_DELETED'), $redirectOptions);
        
    }
    
}