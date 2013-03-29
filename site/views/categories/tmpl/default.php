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
<div class="itp-vp<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php foreach($this->items as $item) {?>
    	<div class="q_category">
    		<?php $categoryParams = json_decode($item->params, true);
    		    $categoryImage = JArrayHelper::getValue($categoryParams, "image");
    		    if($categoryImage) {
    		?>
    		<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->id).$this->tmplValue); ?>">
    		<img src="<?php echo $categoryImage;?>" alt="<?php echo $item->title;?>" />
    		</a><br />
    		<?php } ?>
    		<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->id).$this->tmplValue); ?>">
    		<?php echo $item->title;?>
    		<?php if($this->displayCounter) {
    		    $number = JArrayHelper::getValue($this->numbers, $item->id);
    		?>
    		( <?php echo intval($number); ?> )
    		<?php } ?>
    		</a>
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