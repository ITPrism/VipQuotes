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
<form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="author-form" class="form-validate form-horizontal" enctype="multipart/form-data" >
    
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
        
	</div>
	
	<div class="row-fluid">
    	<div class="span12">
            <fieldset>
                <legend><?php echo JText::_("COM_VIPQUOTES_IMAGE_OPTIONS"); ?></legend>
                <?php echo $this->loadTemplate("image");?>
                
                <input type="hidden" name="task" value="" />
                <?php echo JHtml::_('form.token'); ?>
                
                <?php if (!empty($this->item->image)) {?>
                    <div><img src="<?php echo "../". $this->params->get("images_directory", "images/authors") . "/". $this->item->thumb; ?>" alt="<?php echo $this->item->name;?>" class="img-polaroid" /></div>
                    <br />
                    <div><img src="<?php echo "../". $this->params->get("images_directory", "images/authors") . "/". $this->item->image; ?>" alt="<?php echo $this->item->name;?>" class="img-polaroid" /></div>
                    <br />
                    <a href="<?php echo JRoute::_("index.php?option=com_vipquotes&task=author.removeImage&id=" . $this->item->id); ?>" class="btn btn-small btn-danger">
                        <i class="icon-remove"></i>
                        <?php echo JText::_("COM_VIPQUOTES_REMOVE_IMAGE")?>
                    </a>
            	<?php }?>
            </fieldset>
        </div>
    </div>
    
</form>  
    
