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
<?php foreach($this->items as $item) {
    
    // Prepare social profile link.
    $profileName = "";
    if(!empty($this->displayPublisher)) {
        $socialProfile  = (!$this->socialProfiles) ? null : $this->socialProfiles->getLink($item->user_id);
        $profileName    = JHtml::_("vipquotes.socialProfileLink", $socialProfile, $item->publisher);
    }
    
?>
<div class="row-fluid vq-row">
    <div class="span12">
        <?php if(!empty($this->userId) AND ( $this->userId == $item->user_id)) {?>
    	<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getFormRoute($item->id));?>" class="itp_edit_icon" >
    		<span title="<?php echo JText::_("JGLOBAL_EDIT");?>" class="icon-edit hasTooltip"></span>
    	</a>
        <?php }?>
        
        <blockquote>
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid).$this->tmplValue);?>">
                <?php echo $item->quote; ?>
            </a>
            
            <?php if($this->displayAuthor AND !empty($item->author)) {?>
                <small>
                	<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getAuthorRoute($item->author_slug).$this->tmplValue);?>" class="vq-author"><?php echo $item->author; ?></a>
                </small>
            <?php } ?>
        </blockquote>
    </div>
    
    <?php if($this->displayInfo) {?>
        <div class="clearfix"></div>
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
    <?php }?>
</div>
<?php }?>
    