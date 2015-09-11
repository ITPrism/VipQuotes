<?php
/**
 * @package      VipQuotes
 * @subpackage   Statistics
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Statistics;

defined('JPATH_PLATFORM') or die;

/**
 * This class loads statistics about transactions.
 *
 * @package      VipQuotes
 * @subpackage   Statistics
 */
class Basic
{
    /**
     * Database driver
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $statistics = new VipQuotesStatisticsBasic(JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver  $db Database Driver
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Return the number of all quotes.
     *
     * <code>
     * $statistics = new VipQuotesStatisticsBasic(JFactory::getDbo());
     *
     * $totalQuotes = $statistics->getTotalQuotes()
     * </code>
     *
     * @return int
     */
    public function getTotalQuotes()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__vq_quotes", "a"));

        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        if (!$result) {
            $result = 0;
        }

        return (int)$result;
    }

    /**
     * Return the number of all authors.
     *
     * <code>
     * $statistics = new VipQuotesStatisticsBasic(JFactory::getDbo());
     *
     * $totalAuthors = $statistics->getTotalAuthors()
     * </code>
     *
     * @return int
     */
    public function getTotalAuthors()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__vq_authors", "a"));

        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        if (!$result) {
            $result = 0;
        }

        return (int)$result;
    }
}
