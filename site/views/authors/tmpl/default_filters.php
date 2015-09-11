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
<div class="row">
	<form method="get" action="<?php echo JRoute::_("index.php?option=com_vipquotes&view=authors");?>" id="vq-filters-form"> 
    <?php if($this->filterOrdering) {?>
        <div class="col-md-4">
        <?php echo JHtml::_("select.genericlist", $this->orderingOptions, "filter_author_ordering", array("class" => "js-authors-ordering-filter"), "value", "text", $this->state->get("filter.ordering"));?>
        </div>
    <?php }?>
    
    </form>
</div>
