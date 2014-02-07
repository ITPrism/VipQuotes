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
<div class="vq-quote<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php if($this->params->get("quote_display_hits", 0)) {?>
	<div class="vq-hits"><?php echo JText::sprintf("COM_VIPQUOTES_HITS", $this->item->hits);?></div>
    <?php }?>

    <?php echo $this->item->event->onContentBeforeDisplay;?>
	<blockquote>
		<?php echo $this->item->quote; ?>
    	<?php if($this->params->get("quote_display_author", 1)) {?>
    	
        	<?php if($this->params->get("quote_author_linkable", 1)) {?>
        	<small>
        	   <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getAuthorRoute($this->item->author_slug).$this->tmplValue);?>"><?php echo $this->escape($this->item->author_name); ?></a>
    	    </small>
        	<?php } else {?>
        	<small>
        	   <?php echo $this->echo($this->item->author_name); ?>
    	    </small>
    	    <?php }?>
    	    
	    <?php }?>
	</blockquote>   
	<?php echo $this->item->event->onContentAfterDisplay;?>
</div>
<?php echo $this->version->backlink; ?>