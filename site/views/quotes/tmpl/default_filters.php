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
?>
<form method="get" action="<?php echo JRoute::_("index.php?option=com_vipquotes&view=quotes");?>" id="vq-filters-form" class="form-inline">
    <div class="row">
    <?php if($this->filterAuthor) {?>
        <div class="form-group">
        <?php echo JHtml::_("select.genericlist", $this->authors, "filter_author", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.author"));?>
        </div>
    <?php }?>
    <?php if($this->filterCategory) {?>
        <div class="form-group">
        <?php echo JHtml::_("select.genericlist", $this->categoryOptions, "filter_category", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.category"));?>
        </div>
    <?php }?>
    <?php if($this->filterUser) {?>
        <div class="form-group">
        <?php echo JHtml::_("select.genericlist", $this->users, "filter_user", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.user"));?>
        </div>
    <?php }?>
    <?php if($this->filterOrdering) {?>
        <div class="form-group">
        <?php echo JHtml::_("select.genericlist", $this->orderingOptions, "filter_ordering", array("class" => "js-vqcom-filter"), "value", "text", $this->state->get("filter.ordering"));?>
        </div>
    <?php }?>
    </div>
</form>
<br />

