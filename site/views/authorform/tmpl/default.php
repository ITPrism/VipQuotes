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
<div class="edit author-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading')) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

        <?php echo $this->form->getControlGroup('name'); ?>
        <?php echo $this->form->getControlGroup('bio'); ?>

        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
            <div class="controls">
                <div class="fileupload fileupload-new" data-provides="fileupload">
        <span class="btn btn-file">
            <span class="fileupload-new"><?php echo JText::_("COM_VIPQUOTES_SELECT_FILE"); ?></span>
            <span class="fileupload-exists"><?php echo JText::_("COM_VIPQUOTES_CHANGE"); ?></span>
            <?php echo $this->form->getInput('image'); ?>
        </span>
                    <span class="fileupload-preview"></span>
                    <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <?php echo $this->form->getControlGroup('captcha'); ?>

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
				<span class="icon-plus"></span>
				<?php echo JText::_('COM_VIPQUOTES_SAVE_AND_NEW') ?>
			</button>
		</div>
		
    </form>
    
</div>
<div class="clearfix"></div>
<?php echo $this->version->backlink; ?>