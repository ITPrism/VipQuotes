<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$formItemId = (!empty($this->formItemId)) ? "&Itemid=".$this->formItemId : "";

if (!empty($this->item->image)) {
    $imageExists = true;
    $classStyle  = "col-md-7";
} else {
    $imageExists = false;
    $classStyle  = "col-md-12";
}
?>
<div class="vq-author-page<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php echo $this->item->event->onContentBeforeDisplay;?>
    <div class="media row">
        <?php if($imageExists) {?>
        <div class="pull-left media-image col-md-4">
            <img src="<?php echo $this->imagesFolder ."/". $this->item->image; ?>" alt="<?php echo $this->item->name; ?>" />
            <?php if($this->params->get("author_display_copyright", 0) AND !empty($this->item->copyright)) {
                echo $this->item->copyright;
            }?>
        </div>
        <?php }?>
        
        <div class="media-body <?php echo $classStyle;?>">
            <h2 class="media-heading"><?php echo $this->item->name; ?></h2>  
            <?php echo $this->item->bio; ?>  
        </div>
        <?php if($this->displayQuotesLink OR $this->displayHits){?>
        <div class="clearfix"></div>
        <div class="row vq-info">
        
            <?php if($this->displayQuotesLink) {?>
            <div class="col-md-10">
                <img src="media/com_vipquotes/images/authors_quotes.png" width="16" height="16" alt="<?php echo JText::sprintf("COM_VIPQUOTES_AUTHORS_QUOTES", $this->escape($this->item->name));?>" />
                <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getQuotesRoute()."&filter_author=".(int)$this->item->id);?>">
                    <?php echo JText::sprintf("COM_VIPQUOTES_AUTHORS_QUOTES", $this->escape($this->item->name));?>
                </a>
                <?php if($this->displayNumber){ echo "(".$this->quotesNumber.")"; } ?>
            </div>
            <?php }?>
            
            <?php if($this->displayHits) {?>
        	<div class="col-md-2">
        	   <?php echo JText::sprintf("COM_VIPQUOTES_HITS", $this->item->hits);?>
    	    </div>
            <?php }?>
            
        </div>
        <?php }?>
    </div>
    <?php echo $this->item->event->onContentAfterDisplay;?>
</div>