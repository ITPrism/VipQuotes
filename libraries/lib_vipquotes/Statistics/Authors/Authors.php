<?php
/**
 * @package      VipQuotes
 * @subpackage   Statistics\Authors
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace VipQuotes\Statistics\Authors;

use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This is a base class for quotes statistics.
 *
 * @package      VipQuotes
 * @subpackage   Statistics\Authors
 */
abstract class Authors extends ArrayObject
{
    protected function getQuery()
    {
        $query = $this->db->getQuery(true);

        $query->select("a.id, a.alias, a.name, a.image, a.thumb, a.bio, a.copyright, a.hits");
        $query->select($query->concatenate(array("a.id", "a.alias"), "-") . " AS slug");
        $query->from($this->db->quoteName("#__vq_authors", "a"));

        return $query;
    }
}
