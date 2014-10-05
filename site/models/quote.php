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

class VipQuotesModelQuote extends JModelItem
{
    protected $item;

    /**
     * Model context string.
     *
     * @access    protected
     * @var        string
     */
    protected $context = 'com_vipquotes.quote';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Quote', $prefix = 'VipQuotesTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the object state.
        $this->setState($this->context.'.id', $app->input->get("id"));

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
    }

    /**
     * Method to get an object.
     *
     * @param    integer  $id  The id of the object to get.
     *
     * @return    null|object
     */
    public function getItem($id = null)
    {
        if (!$id) {
            $id = $this->getState($this->context.'.id');
        }

        $storedId = $this->getStoreId($id);
        if (!isset($this->item[$storedId])) {

            // Create a new query object.
            $db    = $this->getDbo();
            $query = $db->getQuery(true);

            $query
                ->select(
                    "a.id, a.quote, a.hits, a.catid, a.author_id, a.published, " .
                    "b.name AS author_name, b.thumb, " .
                    $query->concatenate(array("b.id", "b.alias"), ":") . " AS author_slug"
                )
                ->from($db->quoteName("#__vq_quotes", "a"))
                ->innerJoin($db->quoteName("#__vq_authors", "b") ." ON a.author_id = b.id")
                ->where("a.id=" . (int)$id);

            $db->setQuery($query);
            $result = $db->loadObject();

            // Attempt to load the row.
            if (!empty($result->id)) {
                $this->item[$storedId] = $result;
            }
        }

        return (isset($this->item[$storedId])) ? $this->item[$storedId] : null;
    }

    /**
     * Method to increment the hit counter
     *
     * @param    int  $id      Optional ID.
     *
     * @since    1.5
     */
    public function hit($id = null)
    {
        if (empty($id)) {
            $id = $this->getState('quote.id');
        }

        $table = $this->getTable();
        $table->hit($id);
    }
}
