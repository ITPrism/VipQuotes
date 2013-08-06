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
	<form method="get" action="<?php echo JRoute::_("index.php?option=com_vipquotes&view=category&id=".$this->category->get("id"));?>" id="vq-filters-form"> 
    <?php if($this->filterOrdering) {?>
        <div class="span4">
        <?php echo JHtml::_("select.genericlist", $this->orderingOptions, "filter_ordering", null, "value", "text", $this->state->get("filter.ordering"));?>
        </div>
    <?php }?>
    
    <?php if(!empty($this->tmplValue)) {?>
    	<input type="hidden" name="tmpl" value="component" />
    <?php }?>
    
    </form>
</div>
