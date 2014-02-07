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
<tr>
	<th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
	</th>
    <th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.checkall'); ?>
	</th>
	<th width="1%" style="min-width: 55px" class="nowrap center">
		<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', $this->listDirn, $this->listOrder); ?>
	</th>
    <th width="5%" class="nowrap center hidden-phone">&nbsp;</th>
	<th class="title" >
	     <?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_NAME', 'a.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%" class="center hidden-phone">
	     <?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_HITS', 'a.hits', $this->listDirn, $this->listOrder); ?>
	</th>
    <th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>
	  