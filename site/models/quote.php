<?php
/**
 * @package      ITPrism Components
 * @subpackage   Vip Quotes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class VipQuotesModelQuote extends JModelItem {
    
    protected $item;
    
    /**
     * Model context string.
     *
     * @access	protected
     * @var		string
     */
    protected $_context = 'com_vipquotes.quote';
    
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
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Load the object state.
        $this->setState('quote.id', $app->input->get("id"));
        
        // Load the parameters.
        $params = $app->getParams();
        $this->setState('params', $params);
    }
    
    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function getItem($id = null) {
        
        if (!$id) {
            $id = $this->getState('quote.id');
        }
        
        $storedId = $this->getStoreId($id);
        if (!isset($this->item[$storedId])) {
            
            // Create a new query object.
            $db     = $this->getDbo();
            $query  = $db->getQuery(true);
            
            $query
                ->select("a.id, a.quote, a.hits, a.catid, a.published")
                ->from("#__vq_quotes AS a")
                ->where("a.id=".(int)$id);

            $db->setQuery($query);
            $result = $db->loadObject();
            
            // Attempt to load the row.
            if (!empty($result->id)) {
                $this->item[$storedId] = $result;
            } 
        }
        
        return (isset($this->item[$storedId])) ? $this->item[$storedId] : null;
    }
    
    /**
     * Method to increment the hit counter
     *
     * @param	int		Optional ID.
     * @since	1.5
     */
    public function hit($id = null) {
        
        if (empty($id)) {
            $id = $this->getState('quote.id');
        }
        
        $table = $this->getTable();
        $table->hit($id);
    }
}
