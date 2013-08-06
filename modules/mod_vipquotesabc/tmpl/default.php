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
defined('_JEXEC') or die; 
?>
<div class="vqmod-abc<?php echo $moduleclass_sfx;?>">
    <?php foreach($alphas as $alpha) {
        $activeStyle = "";
        if(!empty($alphaValue) AND (strcmp($alphaValue, $alpha ) == 0)) {
            $activeStyle = " active";
        }
    ?>
    <a href="<?php echo JRoute::_($link."&filter_alpha=".$alpha); ?>" class="btn<?php echo $activeStyle;?>"><?php echo $alpha;?></a>
    <?php }?>
    <a href="<?php echo JRoute::_($link."&filter_alpha="); ?>" class="btn<?php echo $activeStyle;?> hasTooltip" title="<?php echo JText::_("MOD_VIPQUOTESABC_CLEAN_FILTER");?>" >
        <i class="icon-remove"></i>
    </a>
    <a href="javascript: void(0);" class="btn hasTooltip" title="<?php echo $tooltip;?>" >
        <i class="icon-help"></i>
    </a>
</div>