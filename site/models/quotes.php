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

class VipQuotesModelQuotes extends JModelList
{
    protected $categories = null;

    /**
     * Constructor.
     *
     * @param   array $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'quote', 'a.quote',
                'catid', 'a.catid',
                'ordering', 'a.ordering',
                'author', 'b.author'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param string $ordering
     * @param string $direction
     *
     * @return  void
     * @since   1.6
     */
    protected function populateState($ordering = 'ordering', $direction = 'ASC')
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the parameters.
        $params = $app->getParams("com_vipquotes");
        $this->setState('params', $params);

        // Set limit
        $value = $params->get("quotes_quotes_limit", $app->get('list_limit', 20));
        $this->setState('list.limit', $value);

        // Set limitstart
        $value = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);

        // Filters

        // Author
        $value = $app->getUserStateFromRequest($this->context . ".filter.author", "filter_author", 0, "int");
        $this->setState('filter.author', $value);

        // Alphabet
        $value = $app->getUserStateFromRequest($this->context . ".filter.alpha", "filter_alpha", null, "string");
        $value = (!preg_match('/^[a-zA-Z]$/', $value)) ? null : substr($value, 0, 1);
        $this->setState('filter.alpha', $value);

        // Category
        $value = $app->getUserStateFromRequest($this->context . ".filter.category", "filter_category", 0, "int");
        $this->setState('filter.category', $value);

        // User
        $value = $app->getUserStateFromRequest($this->context . ".filter.user", "filter_user", 0, "int");
        $this->setState('filter.user', $value);

        // Ordering
        $filterOrdering = (int)$app->getUserStateFromRequest($this->context . ".filter.ordering", "filter_ordering", 0, "int");
        $this->setState('filter.ordering', $filterOrdering);

        // Get phrase
        $value = JString::trim($app->input->get("filter_phrase"));
        $this->setState('filter.phrase', $value);

        // Ordering state
        $this->prepareOrderingState($filterOrdering);
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.phrase');
        $id .= ':' . $this->getState('filter.category');
        $id .= ':' . $this->getState('filter.author');
        $id .= ':' . $this->getState('filter.alpha');
        $id .= ':' . $this->getState('filter.user');

        return parent::getStoreId($id);
    }

    /**
     * Get the master query for retrieving a list of projects to the model state.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    public function getListQuery()
    {
        // Create a new query object.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.quote, a.hits, a.created, a.published, ' .
                'a.catid, a.ordering, a.user_id, a.author_id, ' .
                'b.name as author, ' . $query->concatenate(array("b.id", "b.alias"), ":") . ' AS author_slug, ' .
                'c.name as publisher'
            )
        );

        $query->from($db->quoteName('#__vq_quotes', 'a'));

        // Join authors
        $query->leftJoin($db->quoteName("#__vq_authors", "b") . " ON a.author_id = b.id");
        $query->innerJoin($db->quoteName("#__users", "c") . " ON a.user_id = c.id");
        $query->leftJoin($db->quoteName("#__categories", "d") . " ON a.catid = d.id");

        $query->where('d.extension = ' . $db->quote("com_vipquotes"));
        $query->where('d.published = 1');

        // Use article state if badcats.id is null, otherwise, force 0 for unpublished
        $query->where('a.published = 1');


        // Filter by a category
        $categoryId = intval($this->getState('filter.category'));
        if (!empty($categoryId)) {
            $query->where('a.catid = ' . (int)$categoryId);
        } else {
            $query->where('a.catid > 0 ');
        }

        // Filter by author
        $authorId = intval($this->getState('filter.author'));
        if (!empty($authorId)) {
            $query->where('a.author_id = ' . (int)$authorId);
        }

        // Filter by user
        $userId = intval($this->getState('filter.user'));
        if (!empty($userId)) {
            $query->where('a.user_id = ' . (int)$userId);
        }

        $alpha = $this->getState('filter.alpha');
        if (!empty($alpha)) {
            $this->prepareFilterAlpha($query, $alpha);
        }

        // Filter by phrase
        $phrase = $this->getState('filter.phrase');
        if (!empty($phrase)) {
            $escaped = $db->escape($phrase, true);
            $quoted  = $db->quote("%" . $escaped . "%", false);
            $query->where('(a.quote LIKE ' . $quoted . ')');
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    /**
     * @param JDatabaseQuery $query
     * @param string $alpha
     */
    protected function prepareFilterAlpha($query, $alpha)
    {
        $db = $this->getDbo();

        // Model parameters
        $module       = JModuleHelper::getModule("mod_vipquotesabc");
        $moduleParams = new Joomla\Registry\Registry;
        /** @var  $moduleParams Joomla\Registry\Registry */

        $moduleParams->loadString($module->params, 'JSON');

        $filterAuthorAlpha   = $moduleParams->get("filter_authors");
        $filterCategoryAlpha = $moduleParams->get("filter_categories");

        $escaped = $db->escape($alpha, true);
        $quoted  = $db->quote($escaped . "%", false);

        if (!empty($filterAuthorAlpha) and !empty($filterCategoryAlpha)) {
            $query->where('(b.name LIKE ' . $quoted . ' OR d.title LIKE ' . $quoted . ')');
        } elseif (!empty($filterAuthorAlpha)) {
            $query->where('(b.name LIKE ' . $quoted . ')');
        } elseif (!empty($filterCategoryAlpha)) {
            $query->where('(d.title LIKE ' . $quoted . ')');
        }

    }

    /**
     * Prepare a string used for ordering results
     *
     * @param integer $filterOrdering
     */
    protected function prepareOrderingState($filterOrdering)
    {
        $listOrder = 'ASC';

        switch ($filterOrdering) {
            case 1:
                $orderCol = "a.created";
                break;

            case 2:
                $orderCol  = "a.created";
                $listOrder = "DESC";
                break;

            case 3:
                $orderCol = "b.name";
                break;

            case 4:
                $orderCol  = "a.hits";
                $listOrder = "DESC";
                break;

            case 5:
                $orderCol  = "b.hits";
                $listOrder = "DESC";
                break;

            default:
                $orderCol = "a.ordering";
                break;
        }

        // Set the column using for ordering
        $this->setState('list.ordering', $orderCol);

        // Set the type of ordering
        if (!in_array(strtoupper($listOrder), array('ASC', 'DESC'))) {
            $listOrder = 'ASC';
        }
        $this->setState('list.direction', $listOrder);

    }

    public function getCategories()
    {
        if (is_null($this->categories)) {

            $db = $this->getDbo();
            /** @var $db JDatabaseDriver */

            // Create a new query object.
            $query = $db->getQuery(true);

            // Select the required fields from the table.
            $query
                ->select('a.id, a.title, a.alias')
                ->from($db->quoteName("#__categories", "a"))
                ->where('a.extension = "com_vipquotes"')
                ->where('a.published = 1');

            $db->setQuery($query);
            $this->categories = $db->loadAssocList("id");

        }

        return $this->categories;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }
}
