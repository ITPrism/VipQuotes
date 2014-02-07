<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Category model.
 *
 * @package		ITPrism Components
 * @subpackage	VipQuotes
 * @since		1.5
 */
class VipQuotesModelPage extends JModelLegacy {
    
    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_VIPQUOTES';
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	1.6
     */
    public function getTable($type = 'Page', $prefix = 'VipQuotesTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * 
     * Load data about Facebook pages and store it into database
     * @param integer $facebookUserId
     * @param Facebook $facebook
     */
    public function update($facebookUserId, $facebook) {
        
        $pages    = VipQuotesHelper::getFacebookPages($facebook);
        
        // Get extra data from Facebook
        if(!empty($pages)) {
            
            foreach($pages as &$page) {
                
                // Collect pages IDs
                $pagesIds[] = JArrayHelper::getValue($page, "id");
            
                try {
                    
                    $fql    =   "
                    	SELECT page_url, pic, pic_square, fan_count, type, is_published
                    	FROM 
                    		page 
                		WHERE 
                			page_id = " . $page["id"];
                    
                    $param  =   array(
                        'method'    => 'fql.query',
                        'query'     => $fql,
                        'callback'  => ''
                    );
                    $fqlResult   =   $facebook->api($param);
                    
                    if(!empty($fqlResult) ) {
                        if(isset($fqlResult[0])) {
                            $page["page_url"]   = JArrayHelper::getValue($fqlResult[0], "page_url");
                            $page["pic"]        = JArrayHelper::getValue($fqlResult[0], "pic");
                            $page["pic_square"] = JArrayHelper::getValue($fqlResult[0], "pic_square");
                            $page["fan_count"]  = JArrayHelper::getValue($fqlResult[0], "fan_count");
                            $page["type"]       = JArrayHelper::getValue($fqlResult[0], "type");
                            $page["published"]  = JArrayHelper::getValue($fqlResult[0], "is_published");
                        }
                    }
                    
                } catch(Exception $e){
                    throw new Exception($e->getMessage(), $e->getCode(), $e);
                }
            }
            
            // Update data
            $this->updateAccounts($pages);
            $this->removeMissedPages($pagesIds);
        }
    }
    
    /**
     * 
     * Store data into database
     * @param array $pages
     */
    private function updateAccounts($pages) {
        
        foreach($pages as $page) {
            
            $table = $this->getTable();
            
            // Load data
            $table->load(array("page_id" => $page["id"]));
            
            // Set the new data
            $table->title         = JArrayHelper::getValue($page, "name");
            $table->page_id       = JArrayHelper::getValue($page, "id");
            $table->page_url      = JArrayHelper::getValue($page, "page_url");
            $table->pic           = JArrayHelper::getValue($page, "pic");
            $table->pic_square    = JArrayHelper::getValue($page, "pic_square");
            $table->fans          = JArrayHelper::getValue($page, "fan_count");
            $table->type          = JArrayHelper::getValue($page, "type");
            $table->published     = JArrayHelper::getValue($page, "published");
                
            $table->store();
            
        }
        
    }
    
    public function removeMissedPages($pagesIds) {
        
        $db = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Remove tabs
        $query = $db->getQuery(true);
        $query
            ->delete($db->quoteName("#__vq_tabs"))
            ->where($db->quoteName("page_id"). " NOT IN ( " . implode(",", $pagesIds) . ")" );
        
        $db->setQuery($query);
        $db->execute();
        
        // Remove pages
        $query = $db->getQuery(true);
        $query
            ->delete($db->quoteName("#__vq_pages"))
            ->where($db->quoteName("page_id") ." NOT IN ( " . implode(",", $pagesIds) . ")" );
        
        $db->setQuery($query);
        $db->execute();

    }
    
}