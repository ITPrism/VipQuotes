<?php
/**
 * @package      ITPrism Modules
 * @subpackage   Vip Random Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Random Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined('_JEXEC') or die; ?>

<div class="vq-modsearch<?php echo $moduleclass_sfx;?>">

    <form method="get" action="<?php echo JRoute::_(VipQuotesHelperRoute::getQuotesRoute());?>" autocomplete="off" >
        
        <div class="control-group">
			<div class="controls">
				<input name="filter_phrase" value="<?php echo $phraseValue;?>" placeholder="<?php echo JText::_("MOD_VIPQUOTESSEARCH_SEARCH_FOR");?>" class="inputbox" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<?php if($params->get("display_categories", 0)) {
                    echo JHtml::_("select.genericlist", $categories, "filter_category", null, "value", "text", $categoryValue);
                }?>
			</div>
		</div>
        
        <button type="submit" class="btn btn-primary" ><?php echo JText::_("MOD_VIPQUOTESSEARCH_SEARCH");?></button>
    </form>
    
</div>