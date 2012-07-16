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
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * It is a quote model
 * 
 * @author Todor Iliev
 * @todo gets the destination dir from parameters
 */
class VipQuotesModelQuote extends JModelAdmin {
    
    /**
     * @var     string  The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_VIPQUOTES';
    
    /**
     * Constructor.
     *
     * @param   array   $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array()){
        parent::__construct($config);
    }
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Quote', $prefix = 'VipQuotesTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array   $data       An optional array of data for the form to interogate.
     * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true){
        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Get the form.
        $form = $this->loadForm('com_vipquotes.quote', 'quote', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
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
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_vipquotes.edit.quote.data', array());
        
        if(empty($data)){
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    /**
     * Save quote data into the DB
     * 
     * @param $data   The data about quote
     * 
     * @return     Item ID
     */
    public function save($data){
        
        $quote     = JArrayHelper::getValue($data, "quote");
        $id        = JArrayHelper::getValue($data, "id");
        $author    = JArrayHelper::getValue($data, "author");
        $catid     = JArrayHelper::getValue($data, "catid");
        $published = JArrayHelper::getValue($data, "published");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        if(!$row->id) {
            $user = JFactory::getUser();
            $row->set("user_id", $user->id);
        }
        
        $row->set("quote", $quote);
        $row->set("author", $author);
        $row->set("catid", $catid);
        $row->set("published", $published);
        
        $row->store();
        
        return $row->id;
    
    }
    
    public function hasDuplication($quote, $itemId = null) {
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("COUNT(*)")
        ->from("#__vq_quotes");
        
        if(!empty($itemId)) {
            $query->where("`id` != " . (int)$itemId );
        }
        
        $query->where("`quote` SOUNDS LIKE " . $db->quote($quote) );
        
        $db->setQuery($query);
        $result = $db->loadResult();
        
        return (bool)$result; 
            
    }
    
    /**
     * Delete records
     *
     * @param array $cids Rows Ids
     */
    public function delete($itemsIds){
        
        $db = JFactory::getDbo();
        
        $tableQuotes   = $db->quoteName('#__vq_quotes');
        $columnId      = $db->quoteName('id');
        
        // Delete records 
        $query = "
			DELETE  
			FROM 
			     $tableQuotes 
			WHERE   
			     $columnId IN ( " . implode(",", $itemsIds) . " )";
        
        $db->setQuery($query);
        $db->query();
    
    }
    
	/**
     * A protected method to get a set of ordering conditions.
     *
     * @param   object  A record object.
     * @return  array   An array of conditions to add to add to ordering queries.
     * @since   1.6
     */
    protected function getReorderConditions($table){
        $condition = array();
        $condition[] = 'catid = '.(int) $table->catid;
        return $condition;
    }
    
}