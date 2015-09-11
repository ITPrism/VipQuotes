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
<div class="edit author-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading')) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

        <div class="form-group">
            <?php echo $this->form->getLabel('name'); ?>
            <?php echo $this->form->getInput('name'); ?>
        </div>

        <div class="form-group">
            <?php echo $this->form->getLabel('image'); ?>
            <?php echo $this->form->getInput('image'); ?>
        </div>

        <div class="form-group">
            <?php echo $this->form->getLabel('bio'); ?>
            <?php echo $this->form->getInput('bio'); ?>
        </div>

        <div class="form-group">
            <?php echo $this->form->getLabel('captcha'); ?>
            <?php echo $this->form->getInput('captcha'); ?>
        </div>

        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

        <?php if($this->params->get("display_author_form_notes", 1)) { ?>
        <p class="sticky"><?php echo JText::_("COM_VIPQUOTES_NOTE_AUTHOR_FORM"); ?></p>
        <?php if(!$this->params->get("security_author_auto_publishing", 0)) { ?>
        <p class="sticky"><?php echo JText::_("COM_VIPQUOTES_NOTE_AUTHOR_FORM_APPROVE"); ?></p>
        <?php } ?>
        <?php } ?>

        <div class="formelm-buttons">
			<button type="button" class="btn btn-primary" id="js-vq-authorform-save2new">
				<span class="glyphicon glyphicon-plus"></span>
				<?php echo JText::_('COM_VIPQUOTES_SAVE_AND_NEW') ?>
			</button>
		</div>
		
    </form>
    
</div>