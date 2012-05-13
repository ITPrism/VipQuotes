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
defined('_JEXEC') or die;?>

<?php 
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'quote.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task);
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($params->get('show_page_heading', 1)) : ?>
    <h1><?php echo $this->escape($params->get('page_heading')); ?></h1>
    <?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	
	<fieldset>
		<legend><?php echo $this->form->getLabel('quote'); ?></legend>
        <?php echo $this->form->getInput('quote'); ?>
	</fieldset>
	
	<fieldset>
		<div class="formelm">
		<?php echo $this->form->getLabel('author'); ?>
		<?php echo $this->form->getInput('author'); ?>
		</div>

		<div class="formelm">
		<?php echo $this->form->getLabel('catid'); ?>
		<?php echo $this->form->getInput('catid'); ?>
		</div>
		
		<div class="formelm-buttons">
    		<button type="button" id="save_quote" ><?php echo JText::_('JSAVE') ?></button>
    		<button type="button" id="cancel_quote"><?php echo JText::_('JCANCEL') ?></button>
    	</div>
    	
	</fieldset>
	<?php echo $this->form->getInput('q_id'); ?>
	<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>
<div class="clr">&nbsp;</div>
<?php echo $this->version->url;?>