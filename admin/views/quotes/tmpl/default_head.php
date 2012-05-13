<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipPorfolio
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipPorfolio is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
?>
<tr>
    <th width="1%">
        <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
    </th>
	<th class="title" >
	     <?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_QUOTE', 'a.quote', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="10%">
		<?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_AUTHOR', 'a.author', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="10%">
	     <?php echo JHtml::_('grid.sort',  'JCATEGORY', 'a.catid', $this->listDirn, $this->listOrder); ?>
    </th>
	<th width="10%">
		<?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_DATE', 'a.date', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="3%">
		<?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_LIKES', 'a.likes', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="3%">
		<?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_VOTES', 'a.votes', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="3%">
		<?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_RATING', 'a.rating', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="10%">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $this->listDirn, $this->listOrder); ?>
        <?php if ($this->saveOrder) {?>
        <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'quotes.saveorder'); ?>
        <?php }?>
    </th>
    <th width="3%"><?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?></th>
    <th width="3%" class="nowrap"><?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?></th>
</tr>
	  