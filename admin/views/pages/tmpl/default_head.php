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
<tr>
    <th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.checkall'); ?>
	</th>
	<th width="1%" style="min-width: 55px" class="nowrap center">
		<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%" class="nowrap center hidden-phone">&nbsp;</th>
    <th class="title">
        <?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
        <?php echo JText::_('COM_VIPQUOTES_LINK'); ?>
    </th>
    <th width="5%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_VIPQUOTES_FANS', 'a.fans', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  