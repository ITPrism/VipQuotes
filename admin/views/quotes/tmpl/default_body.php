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
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td ><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
		<td><a href="<?php echo JRoute::_("index.php?option=com_vipquotes&view=quote&layout=edit&id=".$item->id);?>" >
		<?php echo JHTML::_('string.truncate', $item->quote, 128, true, false); ?>
		</a></td>
		<td class="center">
		   <?php 
		   $category = JArrayHelper::getValue($this->categories, $item->catid); 
		   if(!empty($category)) {
              echo $this->escape($category);
           } else {
               echo JText::_("COM_VIPQUOTES_UNCATEGORISED");
		   }?>
        </td>
        <td class="center"><?php echo intval($item->hits); ?></td>
		<td class="center nowrap"><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?></td>
        <td class="order">
        <?php
            $disabled = $this->saveOrder ?  '' : 'disabled="disabled"';
            if($this->saveOrder) {
            if ($this->listDirn == 'asc') {
                $showOrderUpIcon = (isset($this->items[$i-1]) AND (!empty($this->items[$i-1]->ordering)) AND ( $item->ordering >= $this->items[$i-1]->ordering )) ;
                $showOrderDownIcon = (isset($this->items[$i+1]) AND ($item->ordering <= $this->items[$i+1]->ordering));
            ?>
                <span><?php echo $this->pagination->orderUpIcon($i, $showOrderUpIcon, 'quotes.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, $showOrderDownIcon, 'quotes.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
            <?php } elseif ($this->listDirn == 'desc') {
                $showOrderUpIcon = (isset($this->items[$i-1]) AND ($item->ordering <= $this->items[$i-1]->ordering));
                $showOrderDownIcon = (isset($this->items[$i+1]) AND (!empty($this->items[$i+1]->ordering)) AND ($item->ordering >= $this->items[$i+1]->ordering)); 
            ?>
                <span><?php echo $this->pagination->orderUpIcon($i, $showOrderUpIcon, 'quotes.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, $showOrderDownIcon, 'quotes.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
            <?php } 
        }?>
        <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
        </td>
        <td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, "quotes."); ?></td>
        <td class="center"><?php echo (int)$item->id;?></td>
	</tr>
<?php }?>
	  