<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class VipQuotesViewAuthors extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $state = null;
    protected $items = null;
    protected $pagination = null;

    protected $option = null;

    protected $authors;
    protected $authorsQuotesNumber;
    protected $quotesLink;
    protected $displayFilters;
    protected $filterOrdering;
    protected $orderingOptions;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get("State");
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        /** @var  $params Joomla\Registry\Registry */
        $params = $this->state->get("params");
        $this->params = $params;

        // Get number of quotes for authors.
        if ($this->params->get("authors_display_counter", 1)) {
            $this->authors = new VipQuotes\Author\Authors(JFactory::getDbo());

            $items_ = array();

            foreach ($this->items as $item) {
                $items_[] = Joomla\Utilities\ArrayHelper::fromObject($item);
            }
            $this->authors->setItems($items_);
            $this->authorsQuotesNumber = $this->authors->getQuotesNumber();

            unset($items_);
        }

        $this->quotesLink = VipQuotesHelperRoute::getQuotesRoute();

        $this->prepareFilters();
        $this->prepareDocument();

        parent::display($tpl);
    }

    protected function prepareFilters()
    {
        $this->displayFilters = false;

        // Ordering
        $this->filterOrdering = $this->params->get("authors_display_filter_ordering", 0);
        if ($this->filterOrdering) {
            $this->displayFilters = true;

            $filters = VipQuotes\Filter\Options::getInstance(JFactory::getDbo());
            $this->orderingOptions = $filters->getAuthorsOrdering();
        }
    }

    /**
     * Prepares the document
     */
    protected function prepareDocument()
    {
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_VIPQUOTES_AUTHORS_DEFAULT_PAGE_TITLE'));
        }

        // Set page title
        $title = $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0)) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        $this->document->setTitle($title);

        // Meta Description
        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        // Meta keywords
        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        // Add scripts
        if ($this->displayFilters) {

            // Add scripts
            JHtml::_('bootstrap.tooltip');
            JHtml::_('formbehavior.chosen', '#filter_author_ordering');

            $this->document->addScript('media/' . $this->option . '/js/site/' . strtolower($this->getName()) . '.js');
        }
    }
}
