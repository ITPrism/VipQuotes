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

class VipQuotesViewForm extends JViewLegacy
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
    
    protected $form = null;
    protected $item = null;

    protected $option = null;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }

    public function display($tpl = null)
    {
        // Initialise variables
        $this->state  = $this->get('State');
        $this->form   = $this->get('Form');
        
        /** @var  $params Joomla\Registry\Registry */
        $params = $this->state->get("params");
        $this->params = $params;

        $this->prepareDocument();

        $this->version     = new VipQuotesVersion();

        parent::display($tpl);
    }

    /**
     * Prepare the document
     */
    protected function prepareDocument()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $menus = $app->getMenu();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        if (!$this->params->get("page_heading")) {
            if ($menu) {
                $this->params->def('page_heading', $menu->title);
            } else {
                $this->params->def('page_heading', JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE_FORM'));
            }
        }

        // Prepare page title
        // Get title from the page title option
        $title = $this->params->get("page_title");
        if (!$title) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0)) { // Set site name if it is necessary ( the option 'sitename' = 1 )
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        $this->document->setTitle($title);

        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));

        // Meta keywords
        $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

        // Set breadcrumb title.
        $pathway = $app->getPathway();
        if ($menu) {
            $pathway->addItem($menu->title);
        } else {
            $pathway->addItem(JText::_('COM_VIPQUOTES_DEFAULT_PAGE_TITLE_FORM'));
        }

        // Add scripts
        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');

        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', '.js-vqform-categories');

        $this->document->addScript('media/' . $this->option . '/js/site/' . strtolower($this->getName()) . '.js');

        // Language
        JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
    }
}
