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

class VipQuotesViewQuotes extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    protected $items = null;
    protected $pagination = null;

    protected $option = null;

    protected $userId;
    protected $categories;
    protected $listView;
    protected $displayAuthor;
    protected $displayPublisher;
    protected $displayInfo;
    protected $socialProfiles;
    protected $displayFilters;
    protected $filterAuthor;
    protected $filterCategory;
    protected $filterUser;
    protected $filterOrdering;
    protected $authors;
    protected $users;
    protected $numberOfFilters;
    protected $categoryOptions;
    protected $orderingOptions;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        /** @var  $params Joomla\Registry\Registry */
        $params = $this->state->get("params");
        $this->params = $params;

        $this->userId = JFactory::getUser()->get("id");

        $this->categories = array();
        if ($this->params->get("category_display_category", 1)) {
            $this->categories = VipQuotesHelper::getCategories();
        }

        $this->listView = $this->params->get("quotes_list_view", "table");

        $this->displayAuthor    = $this->params->get("quotes_display_author");
        $this->displayPublisher = $this->params->get("quotes_display_publisher");
        $this->displayInfo      = $this->params->get("quotes_display_info");

        $this->prepareFilters();
        $this->prepareDocument();

        if (!empty($this->displayPublisher)) {
            $socialProfilesBuilder = new Prism\Integration\Profiles\Builder(
                array(
                    "social_platform" => $this->params->get("integration_social_platform"),
                    "users_ids" => VipQuotesHelper::fetchUserIds($this->items)
                )
            );

            $socialProfilesBuilder->build();

            $this->socialProfiles = $socialProfilesBuilder->getProfiles();
        }

        parent::display($tpl);
    }

    protected function prepareFilters()
    {
        // Filters
        $this->filterAuthor   = $this->params->get("quotes_display_filter_author", 0);
        $this->filterCategory = $this->params->get("quotes_display_filter_category", 0);
        $this->filterUser     = $this->params->get("quotes_display_filter_user", 0);
        $this->filterOrdering = $this->params->get("quotes_display_filter_ordering", 0);

        if ($this->filterAuthor or $this->filterUser or $this->filterOrdering or $this->filterCategory) {
            $this->displayFilters = true;
        } else {
            $this->displayFilters = false;
        }

        $this->numberOfFilters = 0;

        if ($this->filterAuthor) {
            $filters = VipQuotes\Filter\Options::getInstance(JFactory::getDbo());

            $this->authors = $filters->getAuthors(array("state" => Prism\Constants::PUBLISHED));

            $option = array(
                "value" => 0,
                "text"  => JText::_("COM_VIPQUOTES_SELECT_AUTHOR")
            );

            array_unshift($this->authors, $option);

            // Increase the number of filters
            $this->numberOfFilters++;
        }

        if ($this->filterUser) {
            $this->users = JHtml::_('user.userlist');
            $option      = array(
                "value" => 0,
                "text"  => JText::_("COM_VIPQUOTES_SELECT_USER")
            );

            array_unshift($this->users, $option);

            // Increase the number of filters
            $this->numberOfFilters++;
        }

        if ($this->filterCategory) {
            $this->categoryOptions = JHtml::_("category.options", "com_vipquotes", array("filter.published" => 1));
            $option                = new stdClass();
            $option->value         = 0;
            $option->text          = JText::_("COM_VIPQUOTES_SELECT_CATEGORY");

            array_unshift($this->categoryOptions, $option);

            // Increase the number of filters
            $this->numberOfFilters++;
        }

        if ($this->filterOrdering) {
            $filters = VipQuotes\Filter\Options::getInstance(JFactory::getDbo());

            $this->orderingOptions = $filters->getQuotesOrdering();

            // Increase the number of filters
            $this->numberOfFilters++;
        }
    }

    /**
     * Prepares the document
     */
    protected function prepareDocument()
    {
        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));

        // Meta keywords
        $this->document->setDescription($this->params->get('menu-meta_keywords'));

        // Scripts
        JHtml::_('bootstrap.framework');
        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select.js-vqcom-filter');

        if ($this->displayFilters) {
            $this->document->addScript('media/' . $this->option . '/js/site/' . strtolower($this->getName()) . '.js');
        }
    }

    private function preparePageHeading()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $menus = $app->getMenu();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        // Prepare page heading
        if ($menu) {

            $pageHeading = $this->params->get('page_heading', $this->params->get('page_title', $menu->title));
            $this->params->set('page_heading', $pageHeading);

        } else {
            $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE_QUOTES_LIST'));
        }

    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page title
        $title = $this->params->get('page_title');

        // Add title before or after Site Name
        if (!$title) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}
