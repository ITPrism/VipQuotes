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
defined('_JEXEC') or die;?>
<div class="itp-vq<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php if($this->params->get("searchForm")) {?>
    <form action="<?php echo JRoute::_("index.php?option=com_vipquotes&view=quotes&catid=".$this->category->get("id")); ?>" method="get" class="vq_search_form">
    	<input type="text" name="q" value="<?php echo $this->escape($this->query);?>" class="inputbox vqsearchbox" />
    	<input type="submit" name="search" value="<?php echo JText::_("COM_VIPQUOTES_SEARCH")?>" class="button" />
    </form> 
    <?php }?>
    
    <div class="row-fluid vp_header">
        <div class="span8"><?php echo JText::_("COM_VIPQUOTES_QUOTE");?></div>
        <div class="span4"><?php echo JText::_("COM_VIPQUOTES_AUTHOR");?></div>
    </div>
    <?php foreach($this->items as $item) {?>
    <div class="row-fluid">
        <div class="span8"><?php echo $item->quote?></div>
        <div class="span4"><?php echo $item->author?></div>
    </div>
	<?php }?>
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
<?php echo $this->version->backlink;?>