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

jimport('joomla.application.component.view');

class VipQuotesViewImport extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $state;
    protected $form;

    protected $option;

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
        $this->form  = $this->get('Form');

        // HTML Helpers
        JHtml::addIncludePath(ITPRISM_PATH_LIBRARY . '/ui/helpers');

        // Add submenu
        VipQuotesHelper::addSubmenu("quotes");

        // Prepare actions
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
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_VIPQUOTES_IMPORT_MANAGER'));

        // Upload
        JToolbarHelper::custom("import.quotes", "upload", "", JText::_("COM_VIPQUOTES_UPLOAD"), false);

        JToolbarHelper::divider();
        JToolbarHelper::cancel('import.cancel', 'JTOOLBAR_CLOSE');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIPQUOTES_IMPORT_MANAGER'));

        // Add scripts
        JHtml::_('bootstrap.framework');
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

        JHtml::_('itprism.ui.bootstrap_fileuploadstyle');

        $this->document->addScript('../media/' . $this->option . '/js/admin/' . JString::strtolower($this->getName()) . '.js');
    }
}
