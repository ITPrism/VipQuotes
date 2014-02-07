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
        
        // Get the form.
        $form = $this->loadForm($this->option.'.quote', 'quote', array('control' => 'jform', 'load_data' => $loadData));
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
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        // Check the session for previously entered form data.
        $data = $app->getUserState($this->option.'.edit.quote.data', array());
        
        if(empty($data)){
            $data = $this->getItem();
            
            // Prime some default values.
			if ($this->getState($this->getName().'.id') == 0) {
				$data->set('catid', $app->input->getInt('catid', $app->getUserState($this->option.'.quotes.filter.category_id')));
				$data->set('author_id', $app->input->getInt('author_id', $app->getUserState($this->option.'.quotes.filter.author_id')));
			}
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
        
        $id        = JArrayHelper::getValue($data, "id");
        $quote     = JArrayHelper::getValue($data, "quote");
        $authorId  = JArrayHelper::getValue($data, "author_id");
        $catid     = JArrayHelper::getValue($data, "catid");
        $published = JArrayHelper::getValue($data, "published");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        // Prepare flags for new item or changes status of the item.
        $isNew          = true;
        $isChangedState = false;
        if(!empty($row->id)) {
            $isNew = false;
            
            if($row->published != $published) {
                $isChangedState = true;
            }
        }
        
        if(!$row->id) {
            $user = JFactory::getUser();
            $row->set("user_id", $user->id);
        }
        
        $row->set("quote",     $quote);
        $row->set("author_id", $authorId);
        $row->set("catid",     $catid);
        $row->set("published", $published);
        
        // Prepare the row for saving
		$this->prepareTable($row);
		
        $row->store();
        
        $this->triggerEventOnAfterSave($row, $isNew, $isChangedState);
        
        return $row->id;
    
    }
    
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
	protected function prepareTable(&$table) {
	    
        // get maximum order number
		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db     = JFactory::getDbo();
				$query  = $db->getQuery(true);
				$query
				    ->select("MAX(a.ordering)")
				    ->from($db->quoteName("#__vq_quotes") . " AS a");
				
			    $db->setQuery($query, 0, 1);
				$max = $db->loadResult();

				$table->ordering = $max+1;
			}
		}
        
	}
	
	protected function triggerEventOnAfterSave($row, $isNew, $isChangedState) {
	
	    // Get properties
	    $item = $row->getProperties();
	    $item = JArrayHelper::toObject($item);
	
	    // Generate context
	    $context = $this->option.'.'.$this->getName();
	
	    // Include the content plugins for the change of state event.
	    $dispatcher = JEventDispatcher::getInstance();
	    JPluginHelper::importPlugin('content');
	     
	    // Trigger the onContentAfterSave event.
	    $results    = $dispatcher->trigger($this->event_after_save, array($context, &$item, $isNew, $isChangedState));
	    if (in_array(false, $results, true)) {
	        throw new RuntimeException(JText::_("COM_VIPQUOTES_ERROR_DURING_PROCESS_STORING_QUOTE"));
	    }
	
	}
	
    public function hasDuplication($quote, $itemId = null) {
        
        $db     = JFactory::getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);
        $query
            ->select("COUNT(*)")
            ->from($db->quoteName("#__vq_quotes", "a"));
        
        if(!empty($itemId)) {
            $query->where("a.id != " . (int)$itemId );
        }
        
        $query->where("a.quote SOUNDS LIKE " . $db->quote($quote) );
        
        $db->setQuery($query);
        $result = $db->loadResult();
        
        return (bool)$result; 
            
    }
    
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table) {
		$condition   = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}
	
}