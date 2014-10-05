<?php
/**
 * @package      VipQuotes
 * @subpackage   Constants
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * VipQuotes constants.
 *
 * @package      VipQuotes
 * @subpackage   Constants
 */
class VipQuotesConstants
{
    // States
    const PUBLISHED   = 1;
    const UNPUBLISHED = 2;
    const TRASHED     = -2;

    // Mail modes - html and plain text.
    const MAIL_MODE_HTML       = true;
    const MAIL_MODE_PLAIN_TEXT = false;

    const CONTEXT_QUOTES = "com_vipquotes.quotes";
}
