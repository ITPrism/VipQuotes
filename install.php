<?php
/**
 * @package      ITPrism Components
 * @subpackage   Vip Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of VipQuotes component
 */
class pkg_vipQuotesInstallerScript {
    /**
     * method to install the component
     *
     * @return void
     */
    public function install($parent) {
    
    }
    
    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($parent) {
    }
    
    /**
     * method to update the component
     *
     * @return void
     */
    public function update($parent) {
    }
    
    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    public function preflight($type, $parent) {
    }
    
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent) {
        
        if(!defined("PATH_VIPQUOTES_COMPONENT_ADMINISTRATION")) {
            define("PATH_VIPQUOTES_COMPONENT_ADMINISTRATION", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_vipquotes");
        }
        
        // Register Component helpers
        JLoader::register("VipQuotesInstallHelper", PATH_VIPQUOTES_COMPONENT_ADMINISTRATION.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."install.php");
    
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        
        $params             = JComponentHelper::getParams("com_vipquotes");
        $this->imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/authors"));
        $this->imagesPath   = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR.$this->imagesFolder );
        $this->bootstrap    = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."com_vipquotes".DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR."bootstrap.min.css" );
    
        $style = '<style>'.file_get_contents($this->bootstrap).'</style>';
        echo $style;
        
        // Create images folder
        if(!is_dir($this->imagesPath)){
            VipQuotesInstallHelper::createImagesFolder($this->imagesPath);
        }
        
        // Start table with the information
        VipQuotesInstallHelper::startTable();
        
        // Requirements
        VipQuotesInstallHelper::addRowHeading(JText::_("COM_VIPQUOTES_MINIMUM_REQUIREMENTS"));
    
        // Display result about verification for existing folder 
        $title  = JText::_("COM_VIPQUOTES_IMAGE_FOLDER");
        $info   = $this->imagesFolder;
        if(!is_dir($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JYES"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for writeable folder 
        $title  = JText::_("COM_VIPQUOTES_WRITABLE_FOLDER");
        $info   = $this->imagesFolder;
        if(!is_writable($this->imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JYES"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for GD library
        $title  = JText::_("COM_VIPQUOTES_GD_LIBRARY");
        $info   = "";
        if(!extension_loaded('gd') AND function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_VIPQUOTES_WARNING"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JON"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for cURL library
        $title  = JText::_("COM_VIPQUOTES_CURL_LIBRARY");
        $info   = "";
        if( !extension_loaded('curl') ) {
            $info   = JText::_("COM_VIPQUOTES_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("COM_VIPQUOTES_WARNING"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JON"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification Magic Quotes
        $title  = JText::_("COM_VIPQUOTES_MAGIC_QUOTES");
        $info   = "";
        if( get_magic_quotes_gpc() ) {
            $info   = JText::_("COM_VIPQUOTES_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success"  , "text" => JText::_("JOFF"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification FileInfo
        $title  = JText::_("COM_VIPQUOTES_FILEINFO");
        $info   = "";
        if( !function_exists('finfo_open') ) {
            $info   = JText::_("COM_VIPQUOTES_FILEINFO_INFO");
            $result = array("type" => "important", "text" => JText::_("JOFF"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);
        
        // Installed extensions
        VipQuotesInstallHelper::addRowHeading(JText::_("COM_VIPQUOTES_INSTALLED_EXTENSIONS"));
        
        // Display result about verification of installed ITPrism Library
        jimport("itprism.version");
        $title  = JText::_("COM_VIPQUOTES_ITPRISM_LIBRARY");
        $info   = "";
        if( !class_exists("ITPrismVersion") ) {
            $info   = JText::_("COM_VIPQUOTES_ITPRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);
        
        // Vip Random Quotes
        $result = array("type" => "success"  , "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_VIP_RANDOM_QUOTES"), $result, JText::_("COM_VIPQUOTES_MODULE"));
        
        // Vip Last Quotes
        $result = array("type" => "success"  , "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_VIP_LAST_QUOTES"), $result, JText::_("COM_VIPQUOTES_MODULE"));
        
        // Vip Quotes Search
        $result = array("type" => "success"  , "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_MODULE_SEARCH"), $result, JText::_("COM_VIPQUOTES_MODULE"));
        
        // Vip Quotes ABC
        $result = array("type" => "success"  , "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_MODULE_ABC"), $result, JText::_("COM_VIPQUOTES_MODULE"));
        
        // Search - Vip Quotes 
        $result = array("type" => "success"  , "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_SEARCH_VIP_QUOTES"), $result, JText::_("COM_VIPQUOTES_PLUGIN"));
        
        // Content - Vip Quotes
        $result = array("type" => "success"  , "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_CONTENT_VIP_QUOTES"), $result, JText::_("COM_VIPQUOTES_PLUGIN"));
        
        // End table
        VipQuotesInstallHelper::endTable();
            
        echo JText::sprintf("COM_VIPQUOTES_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_vipquotes"));
        
    }
}
