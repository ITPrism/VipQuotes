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

class VipQuotesViewAuthor extends JViewLegacy
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

    /**
     * @var object
     */
    protected $item = null;

    protected $pagination = null;

    protected $option = null;

    protected $pageclass_sfx;

    protected $displayNumber;
    protected $displayHits;
    protected $displayQuotesLink;
    protected $quotesNumber;
    protected $imagesFolder;
    protected $tmplValue;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get search phrase
        $this->item = $this->get('Item');

        if (!$this->item) {
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_AUTHOR_DOES_NOT_EXIST"), 404);
        }

        // Initialise variables
        $this->state  = $this->get('State');

        /** @var  $params Joomla\Registry\Registry */
        $params = $this->state->get("params");
        $this->params = $params;

        $this->displayNumber     = $this->params->get("author_display_counter", 0);
        $this->displayHits       = $this->params->get("author_display_hits", 0);
        $this->displayQuotesLink = $this->params->get("author_display_link_quotes", 0);

        // Get quotes number.
        if ($this->displayNumber) {
            jimport("vipquotes.author");
            $author = new VipQuotesAuthor(JFactory::getDbo());
            $data   = JArrayHelper::fromObject($this->item);
            $author->bind($data);
            $this->quotesNumber = (int)$author->getNumberOfQuotes();
        }

        $this->imagesFolder = $this->params->get("images_directory", "images/authors");

        // Prepare TMPL variable
        $tmpl            = $app->input->get->get("tmpl", "");
        $this->tmplValue = "";
        if (strcmp("component", $tmpl) == 0) {
            $this->tmplValue = "&tmpl=component";
        }

        // Hit
        $model = $this->getModel();
        $model->hit($this->item->id);

        // Prepare document
        $this->prepareDocument();
        $this->prepareEvents();

        $this->version     = new VipQuotesVersion();

        parent::display($tpl);
    }

    protected function prepareEvents()
    {
        // Prepare data used by triggers
        $this->item->link        = JRoute::_(VipQuotesHelperRoute::getAuthorRoute($this->item->id) . $this->tmplValue);
        $this->item->title       = $this->document->getTitle();
        $this->item->image_intro = JUri::root() . $this->imagesFolder . "/" . $this->item->thumb;
        $this->item->text        = $this->item->bio;

        // Events
        JPluginHelper::importPlugin('content');
        $dispatcher        = JEventDispatcher::getInstance();
        $this->item->event = new stdClass();
        $offset            = 0;

        $dispatcher->trigger('onContentPrepare', array('com_vipquotes.author', &$this->item, &$this->params, $offset));

        $results                                   = $dispatcher->trigger('onContentBeforeDisplay', array('com_vipquotes.author', &$this->item, &$this->params, $offset));
        $this->item->event->onContentBeforeDisplay = trim(implode("\n", $results));

        $results                                  = $dispatcher->trigger('onContentAfterDisplay', array('com_vipquotes.author', &$this->item, &$this->params, $offset));
        $this->item->event->onContentAfterDisplay = trim(implode("\n", $results));

        // Replace the content of parameter 'quote' with the parameter 'text'
        $this->item->bio = $this->item->text;
        unset($this->item->text);

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
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        if (!empty($this->item->metadesc)) {
            $this->document->setDescription($this->item->metadesc);
        } else {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        // Meta keywords
        if (!empty($this->item->metakey)) {
            $this->document->setMetadata('keywords', $this->item->metakey);
        } else {
            $this->document->setDescription($this->params->get('menu-meta_keywords'));
        }

        // Add item into breadcrumbs
        if ($this->params->get('authors_breadcrumb', 0)) {

            $menu = $app->getMenu()->getActive();
            $mId  = JArrayHelper::getValue($menu->query, "id");

            if (!empty($this->item->name) and ($mId != $this->item->id)) {
                $pathway = $app->getPathway();
                $pathway->addItem($this->item->name);
            }
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

            $seo = $this->params->get("seo_author_as_page_heading");

            switch ($seo) {

                case "1": // Before page heading
                    $pageHeading = $this->item->name . " | " . $this->params->get('page_heading');
                    break;

                case "2": // After page heading
                    $pageHeading = $this->params->get('page_heading') . " | " . $this->item->name;
                    break;

                case "3": // Only category name
                    $pageHeading = $this->item->name;
                    $this->params->set('page_heading', $pageHeading);
                    break;

                default: // NONE
                    $pageHeading = $this->params->get('page_heading', $this->params->get('page_title', $menu->title));
                    break;
            }

            $this->params->set('page_heading', $pageHeading);

        } else {

            if (!empty($this->item->name)) {
                $this->params->def('page_heading', $this->item->name);
            } else {
                $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE'));
            }
        }

    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page title
        if (!$this->params->get("page_title")) {
            $title = $this->item->name;
        } else {

            $seo = $this->params->get("seo_author_to_title");

            switch ($seo) {

                case "1": // Before page title
                    $title = $this->item->name . " | " . $this->params->get("page_title");
                    break;

                case "2": // After page title
                    $title = $this->params->get('page_title') . " | " . $this->item->name;
                    break;

                case "3": // Only category name
                    $title = $this->item->name;
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
