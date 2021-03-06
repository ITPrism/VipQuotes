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

class VipQuotesViewQuote extends JViewLegacy
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

    /**
     * @var object
     */
    protected $item;

    /**
     * @var object
     */
    protected $category;

    protected $option = null;

    protected $userId;
    protected $pageHeading;

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
        $this->state  = $this->get('State');
        $this->item   = $this->get('Item');
        $this->params = $this->state->get("params");
        $this->userId = JFactory::getUser()->get("id");

        if (!$this->item->published) {
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_QUOTE_DOES_NOT_EXIST"), 404);
        }

        // Checking for published category
        $category = JCategories::getInstance('VipQuotes')->get($this->item->catid);
        if (!$category->published) {
            throw new Exception(JText::_("COM_VIPQUOTES_ERROR_CATEGORY_DOES_NOT_EXIST"), 404);
        }

        $this->category = $category;

        // Hit
        $model = $this->getModel();
        $model->hit($this->item->id);

        // Prepare document
        $this->prepareDocument();
        $this->prepareEvents();

        parent::display($tpl);
    }

    protected function prepareEvents()
    {
        $imagesFolder = $this->params->get("images_directory", "images/authors");

        // Prepare data used by plugins.
        $this->item->link  = JRoute::_(VipQuotesHelperRoute::getQuoteRoute($this->item->id, $this->item->catid));
        $this->item->title = $this->document->getTitle();

        $this->item->image_intro = "";
        if (!empty($this->item->thumb)) {
            $this->item->image_intro = JURI::root() . $imagesFolder . "/" . $this->item->thumb;
        }

        // Events
        JPluginHelper::importPlugin('content');
        $dispatcher        = JEventDispatcher::getInstance();
        $this->item->event = new stdClass();
        $offset            = 0;

        $results                                   = $dispatcher->trigger('onContentBeforeDisplay', array('com_vipquotes.quote', &$this->item, &$this->params, $offset));
        $this->item->event->onContentBeforeDisplay = trim(implode("\n", $results));

        $results                                  = $dispatcher->trigger('onContentAfterDisplay', array('com_vipquotes.quote', &$this->item, &$this->params, $offset));
        $this->item->event->onContentAfterDisplay = trim(implode("\n", $results));
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

        // Add category name into breadcrumbs
        if ($this->params->get('category_breadcrumb', 0)) {
            if (!empty($this->category->title)) {

                $pathway = $app->getPathway();

                $menu = $app->getMenu()->getActive();
                $mId  = JArrayHelper::getValue($menu->query, "id");

                if (!empty($this->category->title) and ($mId != $this->category->id)) {
                    $categoryLink = JRoute::_(VipQuotesHelperRoute::getCategoryRoute($this->category->id));
                    $pathway->addItem($this->category->title, $categoryLink);
                }

                $pathway->addItem(JText::_("COM_VIPQUOTES_QUOTE"));
            }
        }
    }

    private function preparePageHeading()
    {
        // Prepare page heading
        $this->pageHeading = JText::sprintf("COM_VIPQUOTES_QUOTE_HEADING", JString::strtolower($this->category->title), $this->item->author_name);
        $this->params->set('page_heading', $this->pageHeading);
    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page title
        $title = JText::sprintf("COM_VIPQUOTES_QUOTE_HEADING", $this->category->title, $this->item->author_name);

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
