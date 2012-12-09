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
<div class="itp-vq<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <div class="row-fluid vp_header">
        <div class="span12"><?php echo JText::_("COM_VIPQUOTES_QUOTE");?></div>
    </div>
    <?php foreach($this->items as $item) {?>
    <div class="row-fluid">
        <div class="span12">
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid));?>"><?php echo $item->quote?></a>
        </div>
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
<?php echo $this->version->backlink; ?>