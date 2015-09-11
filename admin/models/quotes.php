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

class VipQuotesModelQuotes extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array  $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'quote', 'a.quote',
                'author', 'b.name',
                'category', 'd.title',
                'user_name', 'c.name',
                'created', 'a.created',
                'hits', 'a.hits',
                'ordering', 'a.ordering',
                'published', 'a.published'
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // Load the component parameters.
        $params = JComponentHelper::getParams('com_vipquotes');
        $this->setState('params', $params);

        // Load the filter state.
        $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id', 0, 'int');
        $this->setState('filter.category_id', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.author_id', 'filter_author_id', 0, 'int');
        $this->setState('filter.author_id', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.user_id', 'filter_user_id', 0, 'int');
        $this->setState('filter.user_id', $value);

        // List state information.
        parent::populateState('a.ordering', 'asc');
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
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.category_id');
        $id .= ':' . $this->getState('filter.author_id');
        $id .= ':' . $this->getState('filter.user_id');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.quote, a.created, a.hits, ' .
                'a.published, a.ordering, ' .
                'a.catid, a.user_id, a.author_id, ' .
                'b.name as author, ' .
                'c.name as user_name, ' .
                'd.title as category '
            )
        );
        $query->from($db->quoteName('#__vq_quotes', "a"));

        // Join
        $query->leftJoin($db->quoteName('#__vq_authors', "b") . ' ON a.author_id = b.id');
        $query->leftJoin($db->quoteName('#__users', "c") . ' ON a.user_id = c.id');
        $query->leftJoin($db->quoteName('#__categories', "d") . ' ON a.catid = d.id');

        // Filter by user id
        $userId = $this->getState('filter.user_id');
        if (!empty($userId)) {
            $query->where('a.user_id = ' . (int)$userId);
        }

        // Filter by category id
        $categoryId = $this->getState('filter.category_id');
        if (!empty($categoryId)) {
            $query->where('a.catid = ' . (int)$categoryId);
        }

        // Filter by author id
        $authorId = $this->getState('filter.author_id');
        if (!empty($authorId)) {
            $query->where('a.author_id = ' . (int)$authorId);
        }

        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.published = ' . (int)$state);
        } elseif ($state === '') {
            $query->where('(a.published IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote("%" . $escaped . "%", false);
                $query->where('(a.quote LIKE ' . $quoted . ')');
            }
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering', 'a.id');
        $orderDirn = $this->getState('list.direction', 'asc');

        if ($orderCol == 'a.ordering') {
            $orderCol = 'a.catid ' . $orderDirn . ', a.ordering';
        }

        return $orderCol . ' ' . $orderDirn;
    }
}
