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

class VipQuotesModelCategory extends JModelList
{
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
        $value = $params->get("category_quotes_limit", $app->getCfg('list_limit', 20));
        $this->setState('list.limit', $value);

        // Set limitstart
        $value = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);

        // Set the category id
        $this->setState('filter.catid', $app->input->getInt('id'));

        // Filters

        // Author
        $value = $app->getUserStateFromRequest($this->context . ".filter.author", "filter_author", 0);
        $this->setState('filter.author', $value);

        // User
        $value = $app->getUserStateFromRequest($this->context . ".filter.user", "filter_user", 0);
        $this->setState('filter.user', $value);

        // Ordering
        $filterOrdering = (int)$app->getUserStateFromRequest($this->context . ".filter.ordering", "filter_ordering", 0);
        $this->setState('filter.ordering', $filterOrdering);

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
        $id .= ':' . $this->getState('filter.saerch');
        $id .= ':' . $this->getState('filter.catid');

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
                'b.name as author, ' .
                $query->concatenate(array("b.id", "b.alias"), ":") . ' AS author_slug, ' .
                'c.name as publisher'
            )
        );

        $query->from($db->quoteName('#__vq_quotes', 'a'));

        // Join authors
        $query->leftJoin($db->quoteName("#__vq_authors", 'b') . " ON a.author_id = b.id");
        $query->innerJoin($db->quoteName("#__users", 'c') . " ON a.user_id = c.id");

        // Use article state if badcats.id is null, otherwise, force 0 for unpublished
        $query->where('a.published = 1');

        // Filter by a single or group of categories
        $categoryId = intval($this->getState('filter.catid'));
        if (!empty($categoryId)) {
            $query->where('a.catid = ' . (int)$categoryId);
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

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    /**
     *
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

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }
}
