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

class VipQuotesModelExport extends JModelLegacy
{
    public function getData()
    {
        $output = '<?xml version="1.0" encoding="utf-8" ?><vipquotes/>';
        $xml    = new Prism\Xml\Simple($output);

        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        // Select authors
        $query = $db->getQuery(true);
        $query
            ->select(
                'a.id, a.name, a.bio, a.image, a.thumb, ' .
                'a.alias, a.hits, a.ordering, a.published'
            )
            ->from($db->quoteName('#__vq_authors', 'a'))
            ->order("a.id ASC");

        $db->setQuery($query);
        $results = $db->loadAssocList();

        $this->prepareXML($results, "authors", "item", $xml);

        // Select categories
        $query = $db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.alias, a.description, a.metadesc, ' .
                'a.published, a.parent_id, a.created_user_id, a.hits, a.language'
            )
            ->from($db->quoteName('#__categories', 'a'))
            ->where($db->quoteName('extension') . "=" . $db->quote("com_vipquotes"))
            ->order("a.id ASC");

        $db->setQuery($query);
        $results = $db->loadAssocList();
        $this->prepareXML($results, "categories", "item", $xml);

        // Select quotes
        $query = $db->getQuery(true);
        $query
            ->select(
                'a.id, a.quote, a.created, a.published, a.ordering, ' .
                'a.hits, a.author_id, a.catid, a.user_id'
            )
            ->from($db->quoteName('#__vq_quotes', 'a'))
            ->order("a.id ASC");

        $db->setQuery($query);
        $results = $db->loadAssocList();

        $this->prepareXML($results, "quotes", "item", $xml);

        $dom               = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }

    protected function prepareXML($results, $root, $child, &$xml)
    {
        if (!empty($root) and !empty($child)) {

            $cdataItems = array("quote", "bio", "description");

            // Set ROOT item
            $rootItem = $xml->addChild($root);

            foreach ($results as $itemRow) {

                $item = $rootItem->addChild($child);

                foreach ($itemRow as $key => $value) {

                    if (in_array($key, $cdataItems)) { // CDATA
                        $item->$key = null;
                        $item->$key->addCData($value);
                    } else { // Not CDATA
                        $item->addChild($key, $value);
                    }
                }
            }
        }
    }
}
