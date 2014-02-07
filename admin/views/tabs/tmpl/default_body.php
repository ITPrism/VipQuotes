<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center nowrap">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "tabs."); ?>
        </td>
		<td>
			<a href="<?php echo JRoute::_("index.php?option=com_vipquotes&view=tab&layout=edit&id=". $item->id);?>" ><?php echo $item->title; ?></a>
		</td>
		<td class="center hidden-phone">
			<?php echo $item->app_id; ?>
		</td>
		<td class="center hidden-phone">
			<a href="<?php echo $item->page_url."?sk=app_".$item->app_id;?>" target="_blank"><?php echo JText::_("COM_VIPQUOTES_LINK_TO_TAB"); ?></a>
		</td>
        <td class="center nowrap hidden-phone">
            <?php echo $item->id;?>
        </td>
	</tr>
<?php } ?>
	  