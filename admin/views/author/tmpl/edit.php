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
<form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="author-form" class="form-validate form-horizontal" enctype="multipart/form-data" >

    <?php echo JHtml::_('bootstrap.startTabSet', 'vqAuthor', array('active' => 'details')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'vqAuthor', 'details', JText::_('COM_VIPQUOTES_DETAILS')); ?>
    <div class="row-fluid">
    	<div class="span8">
        	
            <fieldset>
                <legend><?php echo JText::_("COM_VIPQUOTES_AUTHOR_OPTIONS"); ?></legend>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
                </div>
    
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
    				<div class="controls">
    			       <div class="fileupload fileupload-new" data-provides="fileupload">
                            <span class="btn btn-file">
                                <span class="fileupload-new"><?php echo JText::_("COM_VIPQUOTES_SELECT_FILE")?></span>
                                <span class="fileupload-exists"><?php echo JText::_("COM_VIPQUOTES_CHANGE")?></span>
                                <?php echo $this->form->getInput('image'); ?>
                            </span>
                            <span class="fileupload-preview"></span>
                            <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                        </div>
    				</div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('bio'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('bio'); ?></div>
                </div>
            </fieldset>
            
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

        <?php if (!empty($this->item->image)) {?>
        <div class="span4">
            <div>
                <img src="<?php echo "../". $this->params->get("images_directory", "images/authors") . "/". $this->item->thumb; ?>" alt="<?php echo $this->item->name;?>" class="img-polaroid" title="<?php echo JText::_("COM_VIPQUOTES_THUMBNAIL"); ?>" />
            </div>
            <br />
            <div>
                <img src="<?php echo "../". $this->params->get("images_directory", "images/authors") . "/". $this->item->image; ?>" alt="<?php echo $this->item->name;?>" class="img-polaroid" title="<?php echo JText::_("COM_VIPQUOTES_STANDARD_PICTURE"); ?>"/>
            </div>
            <br />
            <a href="<?php echo JRoute::_("index.php?option=com_vipquotes&task=author.removeImage&id=" . $this->item->id); ?>" class="btn btn-small btn-danger">
                <i class="icon-remove"></i>
                <?php echo JText::_("COM_VIPQUOTES_REMOVE_IMAGE")?>
            </a>
        </div>
        <?php }?>

	</div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'vqAuthor', 'image', JText::_('COM_VIPQUOTES_IMAGE')); ?>
	<div class="row-fluid">
    	<div class="span12">
            <fieldset>
                <legend><?php echo JText::_("COM_VIPQUOTES_IMAGE_OPTIONS"); ?></legend>
                <?php echo $this->loadTemplate("image");?>
            </fieldset>
        </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.endTabSet'); ?>
</form>  
    
