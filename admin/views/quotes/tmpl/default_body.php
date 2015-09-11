<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	    
	    $disableClassName = '';
	    $disabledLabel	  = '';
	    if (!$this->saveOrder) {
	        $disabledLabel    = JText::_('JORDERINGDISABLED');
	        $disableClassName = 'inactive tip-top';
	    }
	    
	?>
	<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
        <td class="order nowrap center hidden-phone">
    		<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
    			<i class="icon-menu"></i>
    		</span>
    		<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
    	</td>
		<td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "quotes."); ?>
        </td>
		<td>
    		<a href="<?php echo JRoute::_("index.php?option=com_vipquotes&view=quote&layout=edit&id=".$item->id);?>" >
    		<?php echo JHtml::_('string.truncate', $item->quote, 128, true, false); ?>
    		</a>
		</td>
		<td class="center nowrap hidden-phone">
			<a href="<?php echo JRoute::_("index.php?option=com_vipquotes&view=author&layout=edit&id=".$item->author_id);?>" >
			 <?php echo $this->escape($item->author); ?>
			</a>
		</td>
		<td class="center nowrap hidden-phone">
		   <?php 
		   if(!$item->category) {
               echo JText::_("COM_VIPQUOTES_UNCATEGORISED");
           } else {
               echo $this->escape($item->category);
		   }?>
        </td>
        <td class="center nowrap hidden-phone"><?php echo $this->escape($item->user_name); ?></td>
        <td class="center nowrap hidden-phone"><?php echo intval($item->hits); ?></td>
		<td class="center nowrap hidden-phone"><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC3')); ?></td>
        <td class="center hidden-phone"><?php echo (int)$item->id;?></td>
	</tr>
<?php }?>
	  