<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

class VipQuotesTableEmail extends JTable {
    
    public function __construct($db) {
        parent::__construct('#__vq_emails', 'id', $db);
    }
    
}