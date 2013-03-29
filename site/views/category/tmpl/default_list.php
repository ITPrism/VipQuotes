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
$displayCategory     = $this->params->get("category_display_category");
$displayDate         = $this->params->get("category_display_date");

if($displayCategory OR $displayDate) {
    $displayInfo = true;
} else {
    $displayInfo = false;
}
?>
<?php foreach($this->items as $item) {?>
<div class="row-fluid vq-row">
    <div class="span12">
        <?php if(!empty($this->userId) AND ( $this->userId == $item->user_id)) {?>
    	<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getFormRoute($item->id));?>" class="itp_edit_icon" ><img src="media/com_vipquotes/images/icon_edit_16.png" alt="<?php echo JText::_("JGLOBAL_EDIT");?>" /></a>
        <?php }?>
        <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid).$this->tmplValue);?>"><?php echo $item->quote?></a>
    </div>
    
    <?php if($displayInfo) {?>
    	<div class="clearfix"></div>
        
        <?php if($displayDate OR $displayCategory) {?>
        <div class="row-fluid vq-info-row">
            <?php if($displayDate) { ?>
             <div class="span4">
             <?php $date = JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC3'));
             echo JText::sprintf("COM_VIPQUOTES_PUBLISHED_ON_S", $date);?>
             </div>
            <?php }?>
            
            <?php if($displayCategory AND isset($this->categories[$item->catid])) { ?>
             <div class="span4">
                 <?php echo JText::_("COM_VIPQUOTES_CATEGORY"); ?> : <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->catid).$this->tmplValue);?>"><?php echo JArrayHelper::getValue($this->categories[$item->catid], "title");?></a>
             </div>
            <?php }?>
        </div>
        <?php }?>
    <?php }?>
</div>
<?php }?>
    