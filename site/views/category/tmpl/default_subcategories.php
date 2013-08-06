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
<div class="row-fluid">
    <?php foreach($this->subcategories as $item) {?>
	<div class="q_category">
		<?php 
		    $categoryImage = JArrayHelper::getValue($item->params, "image");
		    if($categoryImage) {
		?>
		<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug).$this->tmplValue); ?>">
		<img src="<?php echo $categoryImage;?>" alt="<?php echo $item->title;?>" />
		</a><br />
		<?php } ?>
		<a href="<?php echo JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->slug).$this->tmplValue); ?>">
		<?php echo $item->title;?>
		<?php echo JHtml::_("vipquotes.categoryQuotesNumber", $item->id, $this->displayNumber);?>
		</a>
	</div>
    <?php }?>
</div>
