<?php
/**
 * @package      ITPrism Modules
 * @subpackage   Vip Random Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Random Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined('_JEXEC') or die; ?>
<?php if (!empty($item)) {?>
    <div><?php echo $item->quote;?></div>
    <?php if( $params->get('display_author', 1) ) {?>
        <?php if( $params->get('author_linkable', 1) ) {?>
    	<div><a href="<?php  echo JRoute::_(VipQuotesHelperRoute::getAuthorRoute($item->author_slug)); ?>"><?php echo $item->author;?></a></div>
    	<?php }else{?>
    	<div><strong><?php echo $item->author;?></strong></div>
	    <?php }?>
	<?php }?>
<?php } ?>