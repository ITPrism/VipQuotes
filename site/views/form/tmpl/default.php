<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading')) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

        <div class="form-group">
        <?php echo $this->form->getLabel('author_id'); ?>
        <?php echo $this->form->getInput('author_id'); ?>
        </div>

        <div class="form-group">
        <?php echo $this->form->getLabel('catid'); ?>
        <?php echo $this->form->getInput('catid'); ?>
        </div>

        <div class="form-group">
        <?php echo $this->form->getLabel('quote'); ?>
        <?php echo $this->form->getInput('quote'); ?>
        </div>

        <div class="form-group">
        <?php echo $this->form->getLabel('captcha'); ?>
        <?php echo $this->form->getInput('captcha'); ?>
        </div>

        <div class="form-group">
        <?php echo $this->form->getInput('id'); ?>
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        
        <div class="formelm-buttons">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('form.save')">
				<span class="glyphicon glyphicon-ok"></span>
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" class="btn btn-default" onclick="Joomla.submitbutton('form.save2new')">
				<span class="glyphicon glyphicon-plus"></span>
				<?php echo JText::_('COM_VIPQUOTES_SAVE_AND_NEW') ?>
			</button>
		</div>
		
    </form>
    
</div>