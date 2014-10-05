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
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	    
	    $disableClassName = '';
	    $disabledLabel	  = '';
	    if (!$this->saveOrder) {
	        $disabledLabel    = JText::_('JORDERINGDISABLED');
	        $disableClassName = 'inactive tip-top';
	    }
	    
	?>
	<tr class="row<?php echo $i % 2; ?>">
		
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
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "authors."); ?>
        </td>
        <td class="center hidden-phone">
        <?php if(!empty($item->thumb)) {?>
            <a href="<?php echo JRoute::_("index.php?option=com_vipquotes&view=author&layout=edit&id=".$item->id);?>" >
            <img src="../<?php echo $this->params->get("images_directory", "images/authors")."/".$item->thumb;?>" />
            </a>
        <?php } else {?>
        	<img src="../media/com_vipquotes/images/no_image.png" />
        <?php }?>
		<td class="nowrap">
		  <a href="<?php echo JRoute::_("index.php?option=com_vipquotes&view=author&layout=edit&id=".$item->id);?>" >
		  <?php echo $this->escape($item->name); ?>
		  </a>
		  <div class="small">
		      <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
	      </div>
	      <div class="small">
		      <a href="<?php echo JRoute::_("index.php?option=com_vipquotes&view=quotes&filter_author_id=".$item->id);?>">
		      <?php echo JText::sprintf('COM_VIPQUOTES_QUOTES_N', JArrayHelper::getValue($this->authorsQuotesNumber, $item->id, 0));?>
		      </a>
	      </div>
	    </td>
	    <td class="nowrap center hidden-phone">
		  <?php echo $item->hits;?>
	    </td>
        <td class="nowrap center hidden-phone">
            <?php echo (int)$item->id;?>
        </td>
	</tr>
<?php }?>
	  