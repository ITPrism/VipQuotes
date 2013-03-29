<?php
/**
 * @package      ITPrism Components
 * @subpackage   VipQuotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.model' );

class VipQuotesModelExport extends JModel {
    
    public function getData() {
        
        $db     = $this->getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Select categories
        $query  = $db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.alias, a.description, a.metadesc, ' .
                'a.published, a.parent_id, a.created_user_id, a.hits, a.language')
            ->from($db->quoteName('#__categories').' AS a')
            ->where($db->quoteName('extension')." = " .$db->quote("com_vipquotes"))
            ->order("a.id ASC");
        
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $output = $this->prepareXML($results, "categories", "item", $output);
        
        // Select quotes
        $query  = $db->getQuery(true);
        $query
            ->select(
                'a.id, a.quote, a.created, a.published, a.ordering, ' .
                'a.hits, a.catid, a.user_id')
            ->from($db->quoteName('#__vq_quotes').' AS a')
            ->order("a.id ASC");
        
        $db->setQuery($query);
        $results = $db->loadAssocList();
        
        $output = $this->prepareXML($results, "quotes", "item", $output);
        
        return $output;
    }
    
    protected function prepareXML($results, $root, $child, $output = null) {
        
        if(is_null($output)) {
            $output = '<xml/>';
        }
        $xml = new VipQuotesSimpleXml($output);

        if(!empty($root) AND !empty($child) ) {
            
            $cdataItems = array("quote", "bio", "description");
            
            // Set ROOT item
            $rootItem = $xml->addChild($root);
            
            foreach( $results as $itemRow ) {
                
                $item = $rootItem->addChild($child);
                
                foreach( $itemRow as $key => $value ) {
                    
                    if(in_array($key, $cdataItems)) { // CDATA
                        $item->$key = null;
                        $item->$key->addCData($value);
                    } else { // Not CDATA
                        $item->addChild($key, $value);
                    }
                }
            }
        }
        
        return $xml->asXML();

    } 
}