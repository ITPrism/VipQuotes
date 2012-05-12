<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ( $item ) {?>
<?php 
if ( $quatationMarks ) {
	$item->quote = '"' . $item->quote . '"';
}
echo $item->quote; 
?>
<p><?php echo $item->author; ?></p>
<?php } ?>