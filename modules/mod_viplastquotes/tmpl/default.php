<?php
/**
 * @package      ITPrism Modules
 * @subpackage   Vip Last Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Last Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined('_JEXEC') or die; ?>
<?php 
    if (!empty($items)) {
        foreach($items as $item) {
            echo "<div>$item->quote</div>";
             
            if($params->get('display_author', 1)) {
                if($params->get('author_linkable', 1)) {
                    echo '<div><a href="'. JRoute::_(VipQuotesHelperRoute::getAuthorRoute($item->author_slug)).'">'.$item->author.'</a></div>';
                } else {
                    echo '<div><strong>'.$item->author.'</strong></div>';
                }
            }
            echo '<div style="clear:both;">&nbsp;</div>';
        }
    }
?>