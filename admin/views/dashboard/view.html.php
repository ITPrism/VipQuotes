<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class VipQuotesViewDashboard extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $option = "";

    protected $latestQuotes;
    protected $popularQuotes;
    protected $popularAuthors;
    protected $totalAuthors;
    protected $totalQuotes;
    protected $prismVersion;
    protected $prismVersionLowerMessage;
    protected $version;

    protected $sidebar;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->version = new VipQuotes\Version();

        // Load Prism library version
        if (!class_exists("Prism\\Version")) {
            $this->prismVersion = JText::_("COM_VIPQUOTES_ITPRISM_LIBRARY_DOWNLOAD");
        } else {
            $prismVersion       = new Prism\Version();
            $this->prismVersion = $prismVersion->getShortVersion();

            if (version_compare($this->prismVersion, $this->version->requiredPrismVersion, "<")) {
                $this->prismVersionLowerMessage = JText::_("COM_VIPQUOTES_PRISM_LIBRARY_LOWER_VERSION");
            }
        }

        // Get latest started.
        $this->latestQuotes = new VipQuotes\Statistics\Quotes\Latest(JFactory::getDbo());
        $this->latestQuotes->load(5);

        // Get popular quotes.
        $this->popularQuotes = new VipQuotes\Statistics\Quotes\Popular(JFactory::getDbo());
        $this->popularQuotes->load(5);

        // Get popular authors.
        $this->popularAuthors = new VipQuotes\Statistics\Authors\Popular(JFactory::getDbo());
        $this->popularAuthors->load(5);

        // Get basic data
        $basic              = new VipQuotes\Statistics\Basic(JFactory::getDbo());
        $this->totalQuotes  = $basic->getTotalQuotes();
        $this->totalAuthors = $basic->getTotalAuthors();

        $this->addToolbar();
        $this->addSidebar();
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
        JToolbarHelper::title(JText::_("COM_VIPQUOTES_DASHBOARD"));

        JToolbarHelper::preferences('com_vipquotes');
        JToolbarHelper::divider();

        // Help button
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_VIPQUOTES_HELP_URL'));
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        VipQuotesHelper::addSubmenu($this->getName());
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Method to set up the document properties
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_VIPQUOTES_DASHBOARD_ADMINISTRATION'));
    }
}
