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

class VipQuotesViewCategory extends JViewLegacy
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

    /**
     * @var object
     */
    protected $category;

    protected $userId;
    protected $categories;
    protected $subcategories;
    protected $displayNumber;
    protected $listView;
    protected $displayAuthor;
    protected $displayPublisher;
    protected $displayInfo;
    protected $filterAuthor;
    protected $filterUser;
    protected $filterOrdering;
    protected $authors;
    protected $users;
    protected $orderingOptions;
    protected $displayFilters;
    protected $socialProfiles;

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

        // Check for valid category
        $categoryId = $app->input->getInt("id", 0);
        $category   = null;

        // Checking for published category
        if (!empty($categoryId)) {
            $category = JCategories::getInstance('VipQuotes')->get($categoryId);
            if (!$category->published) {
                throw new Exception(JText::_("COM_VIPQUOTES_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
            }
        } else {
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
        }

        // Initialise variables
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        /** @var  $params Joomla\Registry\Registry */
        $params = $this->state->get("params");
        $this->params = $params;

        $this->category = $category;
        $this->userId   = JFactory::getUser()->get("id");

        $this->categories = array();
        if ($this->params->get("category_display_category", 1)) {
            $this->categories = VipQuotesHelper::getCategories();
        }

        if ($this->params->get("category_display_subcategories", 0)) {
            $this->subcategories = VipQuotesHelper::getSubCategories($categoryId);
            $this->displayNumber = $this->params->get("category_display_subcategories_counter", 0);
        }

        $this->listView = $this->params->get("category_list_view", "table");

        $this->displayAuthor    = $this->params->get("category_display_author");
        $this->displayPublisher = $this->params->get("category_display_publisher");
        $this->displayInfo      = $this->params->get("category_display_info");

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
        $this->filterAuthor   = $this->params->get("category_display_filter_author", 0);
        $this->filterUser     = $this->params->get("category_display_filter_user", 0);
        $this->filterOrdering = $this->params->get("category_display_filter_ordering", 0);

        if ($this->filterAuthor or $this->filterUser or $this->filterOrdering) {
            $this->displayFilters = true;
        } else {
            $this->displayFilters = false;
        }

        if ($this->filterAuthor) {

            $filters       = VipQuotes\Filter\Options::getInstance(JFactory::getDbo());
            $this->authors = $filters->getAuthors(array("state" => Prism\Constants::PUBLISHED));

            $option = array(
                "value" => 0,
                "text"  => JText::_("COM_VIPQUOTES_SELECT_AUTHOR")
            );

            array_unshift($this->authors, $option);
        }

        if ($this->filterUser) {
            $this->users = JHtml::_('user.userlist');
            $option      = array(
                "value" => 0,
                "text"  => JText::_("COM_VIPQUOTES_SELECT_USER")
            );

            array_unshift($this->users, $option);
        }

        if ($this->filterOrdering) {
            $filters = VipQuotes\Filter\Options::getInstance(JFactory::getDbo());
            $this->orderingOptions = $filters->getQuotesOrdering();
        }

    }

    /**
     * Prepares the document
     */
    protected function prepareDocument()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->prepearePageHeading();

        // Prepare page heading
        $this->prepearePageTitle();

        // Meta Description
        if (empty($this->category->metadesc)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_description'));
        } else {
            $this->document->setDescription($this->category->metadesc);
        }

        // Meta keywords
        if (empty($this->category->metakey)) { // Uncategorised
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        } else {
            $this->document->setMetadata('keywords', $this->category->metakey);
        }

        // Add item name into breadcrumbs
        if ($this->params->get('category_breadcrumb', 0)) {

            $menu = $app->getMenu()->getActive();
            $mId  = JArrayHelper::getValue($menu->query, "id");

            if (!empty($this->category->title) and ($mId != $this->category->id)) {
                $pathway = $app->getPathway();
                $pathway->addItem($this->category->title);
            }
        }

        // Add scripts
        JHtml::_('bootstrap.framework');
        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select.js-vqcom-filter');

        if ($this->displayFilters) {
            $this->document->addScript('media/' . $this->option . '/js/site/' . strtolower($this->getName()) . '.js');
        }
    }

    private function prepearePageHeading()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $menus = $app->getMenu();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        // Prepare page heading
        if ($menu) {

            $seo = $this->params->get("seo_cat_as_page_heading");

            switch ($seo) {

                case "1": // Before page heading
                    $pageHeading = $this->category->title . " | " . $this->params->get('page_heading');
                    break;

                case "2": // After page heading
                    $pageHeading = $this->params->get('page_heading') . " | " . $this->category->title;
                    break;

                case "3": // Only category name
                    $pageHeading = $this->category->title;
                    $this->params->set('page_heading', $pageHeading);
                    break;

                default: // NONE
                    $pageHeading = $this->params->get('page_heading', $this->params->get('page_title', $menu->title));
                    break;
            }

            $this->params->set('page_heading', $pageHeading);

        } else {

            if (!empty($this->category->title)) {
                $this->params->def('page_heading', $this->category->title);
            } else {
                $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE'));
            }
        }

    }

    private function prepearePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page title
        if (!$this->params->get("page_title")) {
            $title = $this->category->title;
        } else {

            $seo = $this->params->get("seo_cat_to_title");

            switch ($seo) {

                case "1": // Before page title
                    $title = $this->category->title . " | " . $this->params->get("page_title");
                    break;

                case "2": // After page title
                    $title = $this->params->get('page_title') . " | " . $this->category->title;
                    break;

                case "3": // Only category name
                    $title = $this->category->title;
                    break;

                default: // NONE
                    $title = $this->params->get('page_title');
                    break;
            }

        }

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
