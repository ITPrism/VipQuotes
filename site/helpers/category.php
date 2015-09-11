<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class VipQuotesCategories extends JCategories
{
    public function __construct($options = array())
    {
        $options['table']     = '#__vq_quotes';
        $options['extension'] = 'com_vipquotes';
        parent::__construct($options);
    }
}
