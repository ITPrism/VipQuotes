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
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading')) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
            
        <?php echo $this->form->getLabel('author_id'); ?>
        <?php echo $this->form->getInput('author_id'); ?>
        
        <?php echo $this->form->getLabel('catid'); ?>
        <?php echo $this->form->getInput('catid'); ?>
        
        <?php echo $this->form->getLabel('quote'); ?>
        <?php echo $this->form->getInput('quote'); ?>
        
        <div class="clearfix"></div>
        
        <?php echo $this->form->getLabel('captcha'); ?>
        <?php echo $this->form->getInput('captcha'); ?>
        
        <?php echo $this->form->getInput('id'); ?>
        
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        
        <div class="formelm-buttons">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('form.save')">
				<span class="icon-ok"></span>
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" class="btn" onclick="Joomla.submitbutton('form.save2new')">
				<span class="icon-plus"></span>
				<?php echo JText::_('COM_VIPQUOTES_SAVE_AND_NEW') ?>
			</button>
		</div>
		
    </form>
    
</div>
<div class="clearfix"></div>
<?php echo $this->version->backlink; ?>