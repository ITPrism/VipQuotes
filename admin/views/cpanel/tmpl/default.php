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

?>
<div id="itp-cpanel">
    <div class="itp-cpitem">
        <a rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}" href="<?php echo JRoute::_("index.php?option=com_config&amp;view=component&amp;component=com_vipquotes&amp;path=&amp;tmpl=component");?>" class="modal">
            <img src="../media/com_vipquotes/images/settings_48.png" alt="<?php echo JText::_("COM_VIPQUOTES_SETTINGS");?>" />
            <span><?php echo JText::_("COM_VIPQUOTES_SETTINGS")?></span> 
        </a>
    </div>
    <div class="itp-cpitem">
        <a href="<?php echo JRoute::_("index.php?option=com_categories&extension=com_vipquotes");?>" >
        <img src="../media/com_vipquotes/images/folder_48.png" alt="<?php echo JText::_("COM_VIPQUOTES_CATEGORIES");?>" />
            <span><?php echo JText::_("COM_VIPQUOTES_CATEGORIES")?></span> 
        </a>
    </div>
    <div class="itp-cpitem">
        <a href="<?php echo JRoute::_("index.php?option=com_vipquotes&amp;view=quotes");?>" >
        <img src="../media/com_vipquotes/images/quotes_48.png" alt="<?php echo JText::_("COM_VIPQUOTES_QUOTES");?>" />
            <span><?php echo JText::_("COM_VIPQUOTES_QUOTES")?></span> 
        </a>
    </div>
</div>
<div id="itp-itprism">
    <img src="../media/com_vipquotes/images/logo.png" alt="<?php echo JText::_("COM_VIPQUOTES");?>" />
    <a href="http://itprism.com" title="A Product of ITPrism.com"><img src="../media/com_vipquotes/images/product_of_itprism.png" alt="A Product of ITPrism.com" /></a>
    <p><?php echo JText::_("COM_VIPQUOTES_YOUR_VOTE"); ?></p>
    <p><?php echo JText::_("COM_VIPQUOTES_SPONSORSHIP"); ?></p>
    <p><?php echo JText::_("COM_VIPQUOTES_SUBSCRIPTION"); ?></p>
</div>