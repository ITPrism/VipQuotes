<?php
/**
* @package      ITPrism Components
* @subpackage   Vip Quotes
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Vip Quotes is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_BASE') or die;

/**
 * This class provides functionality for uploading files and   
 * validates the process.
 */
class VipQuotesFileUploadImage extends VipQuotesFileUpload {
    
    protected $mimeTypes;
    protected $imageExtensions;
    
    /**
     * This is a file size from Media Manager
     * @var integer
     */
    protected $maxFileSize;
    
    /**
     * @return the $maxFileSize
     */
    public function getMaxFileSize() {
        return $this->maxFileSize;
    }

	/**
     * @param integer $maxFileSize
     */
    public function setMaxFileSize($maxFileSize) {
        $this->maxFileSize = $maxFileSize;
    }

	/**
     * @return the $mimeTypes
     */
    public function getMimeTypes() {
        return $this->mimeTypes;
    }

	/**
     * @return the $imageExtensions
     */
    public function getImageExtensions() {
        return $this->imageExtensions;
    }

	/**
     * @param array $mimeTypes
     */
    public function setMimeTypes($mimeTypes) {
        $this->mimeTypes = $mimeTypes;
    }

	/**
     * @param array $imageExtensions
     */
    public function setImageExtensions($imageExtensions) {
        $this->imageExtensions = $imageExtensions;
    }

	public function validate(){
        
        parent::validate();
            
	    if($this->fileLength >  $this->maxFileSize) { 
		    
	        $KB   = 1024 * 1024;
		    
		    $info = JText::sprintf("COM_VIPQUOTES_ERROR_MEDIA_FILE_INFOMATION", round($this->fileLength/$KB, 0), round($this->maxFileSize/$KB, 0));
	        
	        // Log error
		    JLog::add($info);
		    throw new Exception(JText::_("COM_VIPQUOTES_ERROR_WARNFILETOOLARGE"), VipQuotesErrors::CODE_WARNING);
		}
		
        jimport('joomla.image.image');
        $imageProperties = JImage::getImageFileProperties($this->file['tmp_name']);
        
        // Check mime type of the file
        if(false === array_search($imageProperties->mime, $this->mimeTypes)){
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_IMAGE_TYPE'));
        }
        
        // Check file extension
        $ext     = JFile::getExt($this->file['name']);
        $ext     = JFile::makeSafe($ext);
        
        if(false === array_search($ext, $this->imageExtensions)){
            throw new Exception(JText::sprintf('COM_VIPQUOTES_ERROR_IMAGE_EXTENSIONS', $ext));
        }
        
    }
}

