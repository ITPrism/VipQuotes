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

/**
 * It is a quote model
 */
class VipQuotesModelQuote extends JModelAdmin
{
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
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.quote', 'quote', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        // Check the session for previously entered form data.
        $data = $app->getUserState($this->option . '.edit.quote.data', array());

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState($this->getName() . '.id') == 0) {
                $data->set('catid', $app->input->getInt('catid', $app->getUserState($this->option . '.quotes.filter.category_id')));
                $data->set('author_id', $app->input->getInt('author_id', $app->getUserState($this->option . '.quotes.filter.author_id')));
            }
        }

        return $data;
    }

    /**
     * Save quote data into the DB
     *
     * @param array $data   The data about quote
     *
     * @return   int  Item ID
     */
    public function save($data)
    {
        $id        = Joomla\Utilities\ArrayHelper::getValue($data, "id");
        $quote     = Joomla\Utilities\ArrayHelper::getValue($data, "quote");
        $authorId  = Joomla\Utilities\ArrayHelper::getValue($data, "author_id");
        $catid     = Joomla\Utilities\ArrayHelper::getValue($data, "catid");
        $published = Joomla\Utilities\ArrayHelper::getValue($data, "published");

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        // Prepare flags for new item or changes status of the item.
        $isNew          = true;
        $isChangedState = false;
        if ($row->get("id")) {
            $isNew = false;

            if ($published != $row->get("published")) {
                $isChangedState = true;
            }
        }

        if (!$row->get("id")) {
            $user = JFactory::getUser();
            $row->set("user_id", $user->id);
        }

        $row->set("quote", $quote);
        $row->set("author_id", $authorId);
        $row->set("catid", $catid);
        $row->set("published", $published);

        // Prepare the row for saving
        $this->prepareTable($row);

        $row->store();

        $this->triggerEventOnAfterSave($row, $isNew, $isChangedState);

        return $row->get("id");

    }

    protected function prepareTable($table)
    {
        // get maximum order number
        if (!$table->get("id")) {

            // Set ordering to the last item if not set
            if (!$table->get("ordering")) {
                $db    = $this->getDbo();
                $query = $db->getQuery(true);
                $query
                    ->select("MAX(a.ordering)")
                    ->from($db->quoteName("#__vq_quotes", "a"));

                $db->setQuery($query, 0, 1);
                $max = $db->loadResult();

                $table->set("ordering", $max + 1);
            }
        }
    }

    /**
     * @param JTable $row
     * @param bool $isNew
     * @param bool $isChangedState
     *
     * @throws RuntimeException
     */
    protected function triggerEventOnAfterSave($row, $isNew, $isChangedState)
    {
        // Get properties
        $item = $row->getProperties();
        $item = Joomla\Utilities\ArrayHelper::toObject($item);

        // Generate context
        $context = $this->option . '.' . $this->getName();

        // Include the content plugins for the change of state event.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');

        // Trigger the onContentAfterSave event.
        $results = $dispatcher->trigger($this->event_after_save, array($context, &$item, $isNew, $isChangedState));
        if (in_array(false, $results, true)) {
            throw new RuntimeException(JText::_("COM_VIPQUOTES_ERROR_DURING_PROCESS_STORING_QUOTE"));
        }

    }

    public function hasDuplication($quote, $itemId = null)
    {
        $db = JFactory::getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);
        $query
            ->select("COUNT(*)")
            ->from($db->quoteName("#__vq_quotes", "a"));

        if (!empty($itemId)) {
            $query->where("a.id != " . (int)$itemId);
        }

        $query->where("a.quote SOUNDS LIKE " . $db->quote($quote));

        $db->setQuery($query);
        $result = $db->loadResult();

        return (bool)$result;

    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param    object $table   A record object.
     *
     * @return    array    An array of conditions to add to add to ordering queries.
     * @since    1.6
     */
    protected function getReorderConditions($table)
    {
        $condition   = array();
        $condition[] = 'catid = ' . (int)$table->catid;

        return $condition;
    }
}
