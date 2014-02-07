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
$imageOptions = $this->form->getFieldset('image_options');
?>
<?php foreach ($imageOptions as $field) { ?>
	<div class="control-group">
		<?php if (!$field->hidden) : ?>
			<div class="control-label">
				<?php echo $field->label; ?>
			</div>
		<?php endif; ?>
		<div class="controls">
			<?php echo $field->input; ?>
		</div>
	</div>
<?php }; ?>

