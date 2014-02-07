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

jimport('joomla.application.component.modeladmin');

class VipQuotesModelForm extends JModelAdmin {
    
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
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        
        $app = JFactory::getApplication("Site");
        /** @var $app JSite **/
        
        // Category state
        $value = $app->getUserStateFromRequest($this->option.".edit.quote.category_id", "catid");
        $this->setState('quote.catid', $value);
        
        // Author state
        $value = $app->getUserStateFromRequest($this->option.".edit.quote.author_id", "author_id");
        $this->setState('quote.author_id', $value);
        
        // Set state of the record id.
        $this->setState('form.id', $app->input->get("id"));
        
        // Load the parameters.
        $params	= $app->getParams($this->option);
		$this->setState('params', $params);
    }
    
    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML and then an event is fired
     * for users plugins to extend the form with extra fields.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm($this->option.'.quote', 'quote', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
		$data	    = $app->getUserState($this->option.'.edit.quote.data', array());
		if(!$data) {
		    $data   = $this->getItem();
		    
		    // If it is a new quote, we will use values from previus save
			if ($this->getState('form.id') == 0) {
				$data->set('catid',     $this->getState("quote.catid"));
				$data->set('author_id', $this->getState("quote.author_id"));
			}
			
		}

		return $data;
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
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The record id on success, null on failure.
     * @since	1.6
     */
    public function save($data, $params = null) {
        
        $id        = JArrayHelper::getValue($data, "id");
        $quote     = JArrayHelper::getValue($data, "quote");
        $authorId  = JArrayHelper::getValue($data, "author_id");
        $catid     = JArrayHelper::getValue($data, "catid");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        // If there is an ID, the item is NOT new.
        $isNew     = true;
        if(!empty($row->id)) {
            $isNew = false;
        }
        
        $user = JFactory::getUser();
        if(!$row->id) {
            $row->set("user_id", $user->id);
            
            // Auto Publishing
            $app = JFactory::getApplication("Site");
        	/** @var $app JSite **/
             
            $params = $app->getParams($this->option);
            if($params instanceof JRegistry) {
                if($params->get("security_auto_publishing", 0)) {
                    $row->set("published", 1);
                }
            }
        } else {
            if($user->id != $row->user_id) {
                throw new Exception(JText::_("COM_VIPQUOTES_ERROR_INVALID_USER"));
            }
        }
        
        $row->set("quote",     $quote);
        $row->set("author_id", $authorId);
        $row->set("catid",     $catid);
        
        $row->store();
        
        // Trigger the event
        $this->triggerEventOnAfterSave($row, $isNew);
        
        return $row->id;
        
    }
    
    protected function triggerEventOnAfterSave($row, $isNew) {
        
        // Get properties
        $item = $row->getProperties();
        $item = JArrayHelper::toObject($item);
        
        // Generate context
        $context = $this->option.'.quote';
        
        // Include the content plugins for the change of state event.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');
         
        // Trigger the onContentAfterSave event.
        $results    = $dispatcher->trigger($this->event_after_save, array($context, &$item, $isNew));
        if (in_array(false, $results, true)) {
            throw new RuntimeException(JText::_("COM_VIPQUOTES_ERROR_DURING_PROCESS_POSTING_QUOTE"));
        }
        
    }

}