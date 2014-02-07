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

// no direct access
defined('_JEXEC') or die;

jimport("itprism.controller.form.backend");

/**
 * Vip Quotes import controller
 *
 * @package     VipQuotes
 * @subpackage  Components
  */
class VipQuotesControllerImport extends ITPrismControllerFormBackend {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Import', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function quotes() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $data    = $this->input->post->get('jform', array(), 'array');
        $file    = $this->input->files->get('jform', array(), 'array');
        $data    = array_merge($data, $file);
        
        $redirectOptions = array(
            "view" => "import",
            "task" => $this->getTask()
        );
        
        $model   = $this->getModel();
        /** @var $model VipQuotesModelImport **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Validate the form
        $validData = $model->validate($form, $data);
        
        // Check for errors.
        if($validData === false){
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }
            
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.archive');
        jimport('itprism.file.upload');
        
        try{
            
            $dataFile        = JArrayHelper::getValue($data, "data");
            
            // Joomla! media extension parameters
            $mediaParams     = JComponentHelper::getParams("com_media");
            
            jimport("itprism.file");
            jimport("itprism.file.uploader.local");
            jimport("itprism.file.validator.size");
            jimport("itprism.file.validator.image");
            
            $file           = new ITPrismFile();
            
            // Prepare size validator.
            $KB             = 1024 * 1024;
            $fileSize       = (int)$app->input->server->get('CONTENT_LENGTH');
            $uploadMaxSize  = $mediaParams->get("upload_maxsize") * $KB;
            
            $sizeValidator  = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);
            
            $file->addValidator($sizeValidator);
            
            // Validate the file
            $file->validate();
            
            $tmpPath  = $app->getCfg("tmp_path");
            $fileName = JFile::makeSafe($file["name"]);;
            $ext      = JString::strtolower( JFile::getExt($fileName) );

            $filePath = JPath::clean( $tmpPath .DIRECTORY_SEPARATOR. $fileName );
            
            // Prepare uploader object.
            $uploader      = new ITPrismFileUploaderLocal($dataFile);
            $uploader->setDestination($filePath);
            
            // Upload temporary file
            $file->setUploader($uploader);
            
            $file->upload();
            
            // Extract file if it is archive
            if(strcmp($ext, "zip") == 0) {
                
                $destFolder  = JPath::clean($tmpPath .DIRECTORY_SEPARATOR. "quotes");
                
                if(JFolder::exists($destFolder)) {
                    JFolder::delete($destFolder);
                }
                
                $filePath    = $model->extractFile($filePath, $destFolder);
                
            } 
            
            $model->validateFileType($filePath);
            
            $resetId  = JArrayHelper::getValue($data, "reset_id", false, "bool");
            $model->importQuotes($filePath, $resetId);
            
        } catch(RuntimeException $e){
            $this->displayWarning($e->getMessage(), $redirectOptions);
            return;
        } catch ( Exception $e ) {
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));
            
        }
        
        $this->displayMessage(JText::_("COM_VIPQUOTES_DATA_IMPORTED"), $redirectOptions);
        
    }
    
    public function cancel() {
        $link = $this->defaultLink."&view=quotes";
        $this->setRedirect( JRoute::_($link, false) );
    }
    
}