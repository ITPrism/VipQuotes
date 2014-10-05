<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class VipQuotesViewTab extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $state;
    protected $item;
    protected $form;

    protected $documentTitle;
    protected $option;

    protected $pageName;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        $this->params = $this->state->get("params");

        $pageId         = $this->state->get("page_id");
        $this->pageName = VipQuotesHelper::getFacebookPageName($pageId);

        // Prepare actions, behaviors, scritps and document
        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew               = ($this->item->id == 0);
        $this->documentTitle = $isNew ? JText::_('COM_VIPQUOTES_TAB_ADD')
            : JText::_('COM_VIPQUOTES_TAB_EDIT');

        JToolbarHelper::title($this->documentTitle);

        JToolbarHelper::apply('tab.apply');
        JToolbarHelper::save2new('tab.save2new');
        JToolbarHelper::save('tab.save');

        if (!$isNew) {
            JToolbarHelper::cancel('tab.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('tab.cancel', 'JTOOLBAR_CLOSE');
        }
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $isNew = ($this->item->id == 0);

        if (!$isNew) {
            $this->document->setTitle(JText::sprintf("COM_VIPQUOTES_TAB_EDIT_ADMINISTRATION", $this->pageName));
        } else {
            $this->document->setTitle(JText::sprintf("COM_VIPQUOTES_TAB_ADD_ADMINISTRATION", $this->pageName));
        }

        // Scripts
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

        JHtml::_('formbehavior.chosen', 'select');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . strtolower($this->getName()) . '.js');
    }
}
