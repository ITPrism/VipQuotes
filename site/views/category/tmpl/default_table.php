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
$displayCategory     = $this->params->get("category_display_category", 1);
$displayDate         = $this->params->get("category_display_date", 0);

if($displayCategory OR $displayDate) {
    $displayInfo = true;
} else {
    $displayInfo = false;
}
?>
<div class="row-fluid vp_header">
    <div class="span12"><?php echo JText::_("COM_VIPQUOTES_QUOTE");?></div>
</div>
<?php foreach($this->items as $item) {?>
<div class="vq-row">
    <div class="row-fluid">
        <div class="span12">
        
        <?php if(!empty($this->userId) AND ( $this->userId == $item->user_id)) {?>
        	<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getFormRoute($item->id));?>" class="itp_edit_icon" ><img src="media/com_vipquotes/images/icon_edit_16.png" alt="<?php echo JText::_("JGLOBAL_EDIT");?>" /></a>
        <?php }?>
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid).$this->tmplValue);?>"><?php echo $item->quote?></a>
        </div>
    </div>
    
    <?php if($displayInfo) {?>
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
</div>
<?php }?>
