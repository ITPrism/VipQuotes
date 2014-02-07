<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="vq-authors<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php if($this->displayFilters) {
        echo $this->loadTemplate("filters");
    }?>
    
    <table class="table table-hover">
    <?php foreach($this->items as $item) {?>
    	<tr>
    		
    		<?php if($this->params->get("authors_display_image")) {?>
    		<td class="itpvq-img-center">
    		  <?php echo JHtml::_("vipquotes.authorImage", $item, $this->params, $this->tmplValue);?>
    		</td>
    		<?php }?>
    		
    		<td>
    		<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getAuthorRoute($item->slug).$this->tmplValue); ?>"><?php echo $item->name;?></a>
    		<?php 
    		if($this->params->get("authors_display_counter", 1)) {
    		    echo "(".JArrayHelper::getValue($this->authorsQuotesNumber, $item->id, 0) .")";
    		}
    		?>
            
            <?php if($this->params->get("author_display_link_quotes", 0)){?>
            <a href="<?php echo JRoute::_($this->quotesLink."&filter_author=".(int)$item->id);?>" title="<?php echo JText::sprintf("COM_VIPQUOTES_AUTHORS_QUOTES", $this->escape($item->name));?>" class="hasTooltip">
                <img src="media/com_vipquotes/images/authors_quotes.png" width="16" height="16" alt="<?php echo JText::sprintf("COM_VIPQUOTES_AUTHORS_QUOTES", $this->escape($item->name));?>" />
            </a>
            <?php }?>
            
    		<?php if(!empty($item->bio)) {?>
    		<p><?php echo JHtml::_("string.truncate", strip_tags($item->bio), $this->params->get("authors_bio_length", 200), true, false);?></p>
    		<?php }?>
                
    		</td>
    	</tr>
    <?php }?>
    </table>
    
    <div class="clr">&nbsp;</div>
    <div class="pagination">
        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>
    
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
    <div class="clr">&nbsp;</div>
</div>
<?php echo $this->version->backlink; ?>