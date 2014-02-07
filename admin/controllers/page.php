<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Facebook Page controller
 *
 * @package		ITPrism Components
 * @subpackage	VipQuotes
 * @since		1.6
 */
class VipQuotesControllerPage extends JControllerLegacy {
    
    // Check the table in so it can be edited.... we are done with it anyway
    private    $defaultLink = 'index.php?option=com_vipquotes';
    
    /**
     * Cancel operations
     *
     */
    public function cancel() {
        $this->setRedirect( JRoute::_($this->defaultLink . "&view=".$this->view_list, false));
    }
    
}