<?php // no direct access
defined('_JEXEC') or die; ?>
<?php if (!empty($item)) {?>
    <?php echo $item->quote;?>
    
    <?php if ( $params->get('display_author', 1) ) {?>
	<p><?php echo $item->author; ?></p>
	<?php }?>
<?php } ?>