<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="vq-categories<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php
    $i = 0;
    $numberOfItems = count($this->items);
    foreach ($this->items as $item) {
        $i++;
        if ($i == 1) {
            echo '<div class="row">';
        }
        ?>
        <div class="col-md-<?php echo $this->columnSize;?>">
            <div class="thumbnail">
                <?php $categoryParams = json_decode($item->params, true);
                    $categoryImage = JArrayHelper::getValue($categoryParams, "image");
                    if($categoryImage) {
                ?>
                <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug)); ?>">
                    <img src="<?php echo $categoryImage;?>" alt="<?php echo $this->escape($item->title);?>" />
                </a>
                <?php } ?>
                <div class="caption">
                    <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug)); ?>">
                    <?php echo $this->escape($item->title);?>
                    <?php echo JHtml::_("vipquotes.categoryQuotesNumber", $item->id, $this->displayNumber);?>
                    </a>
                </div>
            </div>
        </div>
        <?php
        if (($i == $this->itemsPerLine) or ($i == $numberOfItems)) {
            echo '</div>';
            $i = 0;
        }
        ?>
    <?php }?>

    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) { ?>
        <div class="pagination">
            <?php if ($this->params->def('show_pagination_results', 1)) { ?>
                <p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
            <?php } ?>
            <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <?php } ?>
</div>