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
<?php foreach($this->items as $item) {?>
<div class="row-fluid vq-row">
    <div class="span12">
        <blockquote>
            <a href="<?php echo JRoute::_(VipQuotesHelperRoute::getQuoteRoute($item->id, $item->catid).$this->tmplValue);?>">
            <?php echo $item->quote?>
            </a>
        </blockquote>
    </div>
    
    <?php if($this->displayInfo) {?>
    	<div class="clearfix"></div>
        <div class="row-fluid vq-info-row">
            <div class="span12">
            <?php 
            $categoryTitle = JArrayHelper::getValue($this->categories[$item->catid], "title");
            echo Jhtml::_("vipquotes.information", $item, $categoryTitle, $this->displayCategory, $this->displayDate, null, $this->displayHits, $this->tmplValue);
            ?>
            </div>
        </div>
    <?php }?>
</div>
<?php }?>
    