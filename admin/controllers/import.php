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

// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.controllerform' );

/**
 * VipQuotes currency controller
 *
 * @package     ITPrism Components
 * @subpackage  VipQuotes
  */
class VipQuotesControllerImport extends VipQuotesControllerAdminForm {
    
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
        
        $msg     = "";
        $link    = "";
        $task    = $this->getTask();
        $data    = $app->input->post->get('jform', array(), 'array');
        $file    = $app->input->files->get('jform', array(), 'array');
        $data    = array_merge($data, $file);
        
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
            
            $this->defaultLink .= "&view=import";
            
            $this->setMessage($model->getError(), "notice");
            $this->setRedirect(JRoute::_($this->defaultLink, false));
            return;
        }
            
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.archive');
        
        try{
            
            $file     = JArrayHelper::getValue($data, "data");
            
            $upload   = new VipQuotesFileUpload($file);
            $upload->validate();
            
            $tmpPath  = JFactory::getApplication()->getCfg("tmp_path");
            $fileName = JFile::makeSafe($file["name"]);;
            $ext      = JString::strtolower( JFile::getExt($fileName) );

            $filePath = JPath::clean( $tmpPath. DIRECTORY_SEPARATOR . $fileName );
            
            $upload->upload($filePath);
            
            // Extract file if it is archive
            if(strcmp($ext, "zip") == 0) {
                
                $destFolder  = JPath::clean( $app->getCfg("tmp_path") ).DIRECTORY_SEPARATOR."quotes";
                
                if(is_dir($destFolder)) {
                    JFolder::delete($destFolder);
                }
                
                $filePath    = $model->extractFile($filePath, $destFolder);
                
            } 
            
            $model->validateFileType($filePath);
            
            $resetId  = JArrayHelper::getValue($data, "reset_id", false, "bool");
            $model->importQuotes($filePath, $resetId);
            
        } catch ( Exception $e ) {
            
            JLog::add($e->getMessage());
            
            $code = $e->getCode();
            switch($code) {
                
                case VipQuotesErrors::CODE_WARNING:
                    
                    $this->setMessage($e->getMessage(), "notice");
                    $link = $this->defaultLink."&view=import&type=".$task;
                    $this->setRedirect(JRoute::_($link, false));
                    return;
                    
                break;
                
                case VipQuotesErrors::CODE_HIDDEN_WARNING:
                    
                    $this->setMessage(JText::_("COM_VIPQUOTES_ERROR_FILE_CANT_BE_UPLOADED"), "notice");
                    $this->setRedirect(JRoute::_($this->defaultLink."&view=".$task, false));
                    return;
                    
                break;
                
                default:
                    throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'), VipQuotesErrors::CODE_ERROR);
                break;
            }
            
        }
        
        $msg  = JText::_('COM_VIPQUOTES_DATA_IMPORTED');
        $link = $this->defaultLink."&view=import";
        $this->setRedirect(JRoute::_($link, false), $msg);
        
    }
    
    public function cancel() {
        $link = $this->defaultLink."&view=quotes";
        $this->setRedirect( JRoute::_($link, false) );
        
    }
    
}