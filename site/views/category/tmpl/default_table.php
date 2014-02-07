<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid vp_header">
    <?php if($this->displayAuthor) {?>
    <div class="span8"><?php echo JText::_("COM_VIPQUOTES_QUOTE");?></div>
    <div class="span4"><?php echo JText::_("COM_VIPQUOTES_AUTHOR");?></div>
    <?php } else {?>
    <div class="span12"><?php echo JText::_("COM_VIPQUOTES_QUOTE");?></div>
    <?php }?>
</div>
<?php foreach($this->items as $item) {
    // Prepare social profile link.
    $profileName = "";
    if(!empty($this->displayPublisher)) {
        $socialProfile  = (!$this->socialProfiles) ? null : $this->socialProfiles->getLink($item->user_id);
        $profileName    = JHtml::_("vipquotes.socialProfileLink", $socialProfile, $item->publisher);
    }
?>
<div class="vq-row">
    <div class="row-fluid">
        <?php if($this->displayAuthor) {?>
        <div class="span8">
        <?php }else {?>
        <div class="span12">
        <?php }?>
        
            <?php if(!empty($this->userId) AND ( $this->userId == $item->user_id)) {?>
        	<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getFormRoute($item->id));?>" class="itp_edit_icon" >
        		<span title="<?php echo JText::_("JGLOBAL_EDIT");?>" class="icon-edit hasTooltip"></span>
        	</a>
            <?php }?>
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid).$this->tmplValue);?>"><?php echo $item->quote?></a>
        </div>
        
        <?php if($this->displayAuthor) {?>
        <div class="span4 itp-center">
            <?php if(!empty($item->author)) { ?>
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getAuthorRoute($item->author_slug).$this->tmplValue);?>"><?php echo $item->author; ?></a>
            <?php } else {?>
            &nbsp;
            <?php }?>
        </div>
        <?php }?>
    </div>
    
    <?php if($this->displayInfo) {?>
    <div class="row-fluid vq-info-row">
        <div class="span12">
        <?php 
        $categoryTitle = JArrayHelper::getValue($this->categories[$item->catid], "title");
        echo Jhtml::_("vipquotes.information", $item, $categoryTitle, $this->tmplValue, $this->displayPublisher, $profileName);
        ?>
        </div>
    </div>
    <?php }?>
</div>
<?php }?>
