<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of VipQuotes component
 */
class pkg_vipQuotesInstallerScript
{
    /**
     * method to install the component
     *
     * @return void
     */
    public function install($parent)
    {

    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * method to update the component
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent)
    {

        if (!defined("PATH_VIPQUOTES_COMPONENT_ADMINISTRATION")) {
            define("PATH_VIPQUOTES_COMPONENT_ADMINISTRATION", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_vipquotes");
        }

        // Register Component helpers
        JLoader::register("VipQuotesInstallHelper", PATH_VIPQUOTES_COMPONENT_ADMINISTRATION . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "install.php");

        jimport('Prism.init');
        jimport('VipQuotes.init');

        $params       = JComponentHelper::getParams("com_vipquotes");
        $imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/authors"));
        $imagesPath   = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . $imagesFolder);

        // Create images folder
        if (!is_dir($imagesPath)) {
            VipQuotesInstallHelper::createImagesFolder($imagesPath);
        }

        // Start table with the information
        VipQuotesInstallHelper::startTable();

        // Requirements
        VipQuotesInstallHelper::addRowHeading(JText::_("COM_VIPQUOTES_MINIMUM_REQUIREMENTS"));

        // Display result about verification for existing folder
        $title = JText::_("COM_VIPQUOTES_IMAGE_FOLDER");
        $info  = $imagesFolder;
        if (!is_dir($imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writeable folder
        $title = JText::_("COM_VIPQUOTES_WRITABLE_FOLDER");
        $info  = $imagesFolder;
        if (!is_writable($imagesPath)) {
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Display result about verification for GD library
        $title = JText::_("COM_VIPQUOTES_GD_LIBRARY");
        $info  = "";
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_VIPQUOTES_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_("COM_VIPQUOTES_CURL_LIBRARY");
        $info  = "";
        if (!extension_loaded('curl')) {
            $info   = JText::_("COM_VIPQUOTES_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("COM_VIPQUOTES_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_("COM_VIPQUOTES_MAGIC_QUOTES");
        $info  = "";
        if (get_magic_quotes_gpc()) {
            $info   = JText::_("COM_VIPQUOTES_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JOFF"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_("COM_VIPQUOTES_FILEINFO");
        $info  = "";
        if (!function_exists('finfo_open')) {
            $info   = JText::_("COM_VIPQUOTES_FILEINFO_INFO");
            $result = array("type" => "important", "text" => JText::_("JOFF"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Display information about PHP version.
        $title = JText::_("COM_VIPQUOTES_PHP_VERSION");
        $info  = "";
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            $result = array("type" => "important", "text" => JText::_("COM_VIPQUOTES_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Installed extensions
        VipQuotesInstallHelper::addRowHeading(JText::_("COM_VIPQUOTES_INSTALLED_EXTENSIONS"));

        // Display result about verification of installed Prism Library
        $title = JText::_("COM_VIPQUOTES_PRISM_LIBRARY");
        $info  = "";
        if (!class_exists("Prism\\Version")) {
            $info   = JText::_("COM_VIPQUOTES_PRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        VipQuotesInstallHelper::addRow($title, $result, $info);

        // Content - Vip Quotes - Admin Mail
        $result = array("type" => "success", "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_PLUGIN_ADMIN_MAIL"), $result, JText::_("COM_VIPQUOTES_PLUGIN"));

        // Content - Vip Quotes - User Mail
        $result = array("type" => "success", "text" => JText::_("COM_VIPQUOTES_INSTALLED"));
        VipQuotesInstallHelper::addRow(JText::_("COM_VIPQUOTES_PLUGIN_USER_MAIL"), $result, JText::_("COM_VIPQUOTES_PLUGIN"));

        // End table
        VipQuotesInstallHelper::endTable();

        echo JText::sprintf("COM_VIPQUOTES_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_vipquotes"));

        if (!class_exists("Prism\\Version")) {
            echo JText::_("COM_VIPQUOTES_MESSAGE_INSTALL_PRISM_LIBRARY");
        } else {

            if (class_exists("VipQuotes\\Version")) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new VipQuotes\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, "<")) {
                    echo JText::_("COM_VIPQUOTES_MESSAGE_INSTALL_PRISM_LIBRARY");
                }
            }
        }
    }
}
