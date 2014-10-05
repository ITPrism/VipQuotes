<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Control Panel Controller
 *
 * @package        ITPrism Components
 * @subpackage     VipQuotes
 */
class VipQuotesController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        $document = JFactory::getDocument();
        /** @var $document JDocumentHtml */

        // Add component style
        $document->addStyleSheet('../media/com_vipquotes/css/backend.style.css');

        $viewName = JFactory::getApplication()->input->getCmd('view', 'dashboard');
        JFactory::getApplication()->input->set("view", $viewName);

        parent::display();

        return $this;
    }
}
