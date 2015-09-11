<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
	<div class="span12 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_vipquotes'); ?>" method="post" name="adminForm" id="quote-form" class="form-validate" >
        
            <fieldset>
                <?php echo $this->form->getControlGroup('author_id'); ?>
                <?php echo $this->form->getControlGroup('catid'); ?>
                <?php echo $this->form->getControlGroup('published'); ?>
                <?php echo $this->form->getControlGroup('id'); ?>
                <?php echo $this->form->getControlGroup('quote'); ?>
            </fieldset>
        
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
	</div>
</div>