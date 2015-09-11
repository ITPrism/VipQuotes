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
 * This class contains methods used
 * in the installation process of the extension.
 *
 */
class VipQuotesInstallHelper
{
    public static function startTable()
    {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered table-striped">';
    }

    /**
     * Display an HTML code for a row
     *
     * @param string $title
     * @param array  $result
     * @param string $info
     *
     * <code>
     * $result = array(
     *    type => success, important, warning,
     *    text => yes, no, off, on, warning,...
     * );
     * </code>
     */
    public static function addRow($title, $result, $info)
    {
        $outputType = JArrayHelper::getValue($result, "type", "");
        $outputText = JArrayHelper::getValue($result, "text", "");

        $output = "";
        if (!empty($outputType) and !empty($outputText)) {
            $output = '<span class="label label-' . $outputType . '">' . $outputText . '</span>';
        }

        echo '
	    <tr>
            <td>' . $title . '</td>
            <td>' . $output . '</td>
            <td>' . $info . '</td>
        </tr>';
    }

    public static function addRowHeading($heading)
    {
        echo '
	    <tr class="info">
            <td colspan="3">' . $heading . '</td>
        </tr>';
    }

    public static function endTable()
    {
        echo "</table></div>";
    }

    public static function createImagesFolder($imagesPath)
    {
        // Create image folder
        if (true !== JFolder::create($imagesPath)) {
            JLog::add(JText::sprintf("COM_VIPQUOTES_ERROR_CANNOT_CREATE_FOLDER", $imagesPath));
        } else {

            // Copy index.html
            $indexFile = $imagesPath . DIRECTORY_SEPARATOR . "index.html";
            $html      = '<html><body style="background-color: #fff;"></body></html>';
            if (true !== JFile::write($indexFile, $html)) {
                JLog::add(JText::sprintf("COM_VIPQUOTES_ERROR_CANNOT_SAVE_FILE", $indexFile));
            }

        }

    }
}
