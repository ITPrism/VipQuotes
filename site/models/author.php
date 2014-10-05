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

class VipQuotesModelAuthor extends JModelItem
{
    protected $item;

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
    public function getTable($type = 'Author', $prefix = 'VipQuotesTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
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
        $app = JFactory::getApplication("Site");
        /** @var $app JApplicationSite */

        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        // Set the author id
        $value = $app->getUserStateFromRequest($this->option . ".author.id", "id");
        $this->setState('author.id', $value);

    }

    public function getItem($id = null)
    {
        if (!$id) {
            $id = $this->getState("author.id");
        }

        $storedId = $this->getStoreId($id);
        if (is_null($this->item[$storedId])) {

            // Get a level row instance.
            $table = $this->getTable();
            $table->load($id);

            // Attempt to load the row.
            if ($table->get("id")) {

                if (!$table->get("published")) {
                    return null;
                }

                // Convert the JTable to a clean JObject.
                $properties            = $table->getProperties(true);
                $this->item[$storedId] = JArrayHelper::toObject($properties);
            }
        }

        return (isset($this->item[$storedId])) ? $this->item[$storedId] : null;

    }

    /**
     * Method to increment the hit counter
     *
     * @param    int   $id     Optional ID.
     *
     * @since    1.5
     */
    public function hit($id = null)
    {
        if (empty($id)) {
            $id = $this->getState('author.id');
        }

        $table = $this->getTable();
        $table->hit($id);
    }
}
