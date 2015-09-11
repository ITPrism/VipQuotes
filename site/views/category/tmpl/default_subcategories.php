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

$i = 0;
$numberOfItems = count($this->subcategories);

foreach($this->subcategories as $item) {
    $i++;
    if ($i == 1) {
        echo '<div class="row">';
    }
    ?>
	<div class="col-md-4">
	    <div class="thumbnail">
		<?php
        $categoryImage = JArrayHelper::getValue($item->params, "image");
        if ($categoryImage) {
		?>
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug)); ?>">
            <img src="<?php echo $categoryImage;?>" alt="<?php echo $item->title;?>" />
            </a>
		<?php } ?>
            <div class="caption">
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug)); ?>">
            <?php echo $item->title;?>
            <?php echo JHtml::_("vipquotes.categoryQuotesNumber", $item->id, $this->displayNumber);?>
            </a>
            </div>
	    </div>
	</div>

    <?php
    if (($i == 4) or ($i == $numberOfItems)) {
        echo '</div>';
        $i = 0;
    }
    ?>
<?php }?>
