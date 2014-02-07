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
<div class="row-fluid">
	<form method="get" action="<?php echo JRoute::_("index.php?option=com_vipquotes&view=quotes");?>" id="vq-filters-form"> 
    <?php if($this->filterAuthor) {?>
        <div class="<?php echo $this->spanClass;?>">
        <?php echo JHtml::_("select.genericlist", $this->authors, "filter_author", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.author"));?>
        </div>
    <?php }?>
    <?php if($this->filterCategory) {?>
        <div class="<?php echo $this->spanClass;?>">
        <?php echo JHtml::_("select.genericlist", $this->categoryOptions, "filter_category", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.category"));?>
        </div>
    <?php }?>
    <?php if($this->filterUser) {?>
        <div class="<?php echo $this->spanClass;?>">
        <?php echo JHtml::_("select.genericlist", $this->users, "filter_user", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.user"));?>
        </div>
    <?php }?>
    <?php if($this->filterOrdering) {?>
        <div class="<?php echo $this->spanClass;?>">
        <?php echo JHtml::_("select.genericlist", $this->orderingOptions, "filter_ordering", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.ordering"));?>
        </div>
    <?php }?>
    
    <?php if( !empty($this->tmplValue) ) {?>
    	<input type="hidden" name="tmpl" value="component" />
    <?php }?>
    </form>
</div>
