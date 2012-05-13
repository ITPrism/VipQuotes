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
defined('_JEXEC') or die();

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

class VipQuotesModelForm extends JModelForm {
    
    protected $item = null;
    
    /**
     * Method to get the password reset request form.
     *
     * @param	array	$data		Data for the form.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm('com_vipquotes.quote', 'quote', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	2.5
     */
    /*protected function loadFormData() {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_vipquotes.edit.quote.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }*/
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     * @since	2.5
     */
    public function getTable($type = 'Quote', $prefix = 'VipQuotesTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Get the message
     * @return object The message to be displayed to the user
     */
    public function getItem() {
        
        if (!isset($this->item)) {
            $cache = JFactory::getCache('com_vipquotes', '');
            $id = $this->getState('com_vipquotes.id');
            $this->item = $cache->get($id);
            if ($this->item === false) {
                $row = $this->getTable();
                $row->load($id);
                $this->item = $row;
            }
        }
        return $this->item;
    
    }
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        
        $app = JFactory::getApplication();
        
        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
        
        // Load state from the request.
        $pk = JRequest::getInt('q_id');
        $this->setState('com_vipquotes.id', $pk);
        // Add compatibility variable for default naming conventions.
        $this->setState('form.id', $pk);
        
        $categoryId = JRequest::getInt('catid');
        $this->setState('com_vipquotes.catid', $categoryId);
        
        // Return page
        $return = JRequest::getVar('return', null, 'default', 'base64');
        if (! JUri::isInternal(base64_decode($return))) {
            $return = null;
        }
        $this->setState('return_page', base64_decode($return));
        
    }
    
    /**
     * Get the return URL.
     *
     * @return	string	The return URL.
     * @since	1.6
     */
    public function getReturnPage() {
        return base64_encode($this->getState('return_page'));
    }
    
	/**
     * Save quote data into the DB
     * 
     * @param $data   The data about quote
     * 
     * @return     Item ID
     */
    public function save($data){
        
        $id        = JArrayHelper::getValue($data, "id");
        $quote     = JArrayHelper::getValue($data, "quote");
        $author    = JArrayHelper::getValue($data, "author");
        $catid     = JArrayHelper::getValue($data, "catid");
        $published = JArrayHelper::getValue($data, "published");
        $userId    = JArrayHelper::getValue($data, "user_id");
        
        if(!isset($this->item)) {
            // Load a record from the database
            $row = $this->getTable();
            $row->load($id);
            $this->item = $row;
            
        }
        
        if(!$this->item->id) {
            $this->item->set("user_id", $userId);
        }
        
        $this->item->set("quote", $quote);
        $this->item->set("author", $author);
        $this->item->set("catid", $catid);
        $this->item->set("published", $published);
        
        $this->item->store();
    
    }
    
    public function validateQuote($id, $userId) {
        
        if(!isset($this->item)) {
            // Load a record from the database
            $row = $this->getTable();
            $row->load($id);
            $this->item = $row;
        }
        
        if($this->item->user_id != $userId) {
            return false;
        }
        
    }

}