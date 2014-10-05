<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
