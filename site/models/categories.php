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

jimport('joomla.application.component.modellist');

class VipQuotesModelCategories extends JModelList {
    
    protected $items   = null;
    protected $numbers = null;
    protected $params  = null;
    
    /**
     * Constructor.
     *
     * @param   array   An optional associative array of configuration settings.
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array()){
        if(empty($config['filter_fields'])){
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'ordering', 'a.ordering'
            );
        }
        
        parent::__construct($config);
    }
    
	/**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return  void
     * @since   1.6
     */
    protected function populateState($ordering = 'ordering', $direction = 'ASC'){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Load parameters
        $params     =  $app->getParams("com_vipquotes");
        $this->setState('params', $params);
        
        // Set limit
        $limit      = $params->get("categories_limit", $app->getCfg('list_limit', 20));
        $this->setState('list.limit', $limit);
        
        $value      = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);
        
        // Ordering state
        $this->prepareOrderingState($params);
        
    }
    
	/**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string      $id A prefix for the store id.
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '') {
        
        // Compile the store id.
        $id.= ':' . $this->getState('list.ordering');
        $id.= ':' . $this->getState('list.direction');

        return parent::getStoreId($id);
    }
    
   /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery() {
        
        $db     = $this->getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        // Create a new query object.
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.alias, a.description, ' .
                'a.metadesc, a.metakey, a.params '
            )
        );
        $query->from('`#__categories` AS a');

        // Filter by state
        $query->where('a.extension = "com_vipquotes"');
        $query->where('a.published = 1');

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }
    
    /*public function getItems( $recursive = false ){
        
        if (!count($this->items)) {
			
			// import Joomla Categories library
			//if you forget this -> Fatal error: Class 'JCategories' not found in ...
			jimport( 'joomla.application.categories' );

			$options = array();

			$categories   = JCategories::getInstance('VipQuotes', $options);
			$this->parent = $categories->get('root');

			if (is_object($this->parent)) {
				$this->items = $this->parent->getChildren($recursive);
			}
			else {
				$this->items = false;
			}
		}

		return $this->items;
		
    }*/
    
    public function getNumbers() {
        
        if (!count($this->numbers)) {
            $db     = JFactory::getDbo();
            $query  = $db->getQuery(true);
            
            $query->select("
            	catid, 
            	COUNT(*) AS number"
            )
            ->from("#__vq_quotes")
            ->where("published = 1")
            ->group("catid");
            
            $db->setQuery($query);
            $results = $db->loadAssocList("catid", "number");
            
            if(!$results) {
                $results = null; 
            }
            
            $this->numbers = $results;
        }
        
        return $this->numbers;
        
    }
    
    protected function prepareOrderingState($params) {
        
        $listOrder = 'ASC';
        
        switch($params->get("categories_order_by", 0)) {
            
            case 1: // Date
                $orderCol = "a.created_time";
                break;

            case 2: // Date Reverse
                $orderCol  = "a.created_time";
                $listOrder = "DESC";
                break;

            case 3: // Name
                $orderCol = "a.title";
                break;
                
            default: // Ordering
                $orderCol = "a.lft";
                break;
        }
        
        $this->setState('list.ordering', $orderCol);
        
        // Set the type of ordering
        if(!in_array(strtoupper($listOrder), array('ASC', 'DESC'))){
            $listOrder = 'ASC';
        }
        $this->setState('list.direction', $listOrder);
        
    }
    
    protected function getOrderString() {
        
        $orderCol   = $this->getState('list.ordering');
        $orderDirn  = $this->getState('list.direction');
        
        return $orderCol.' '.$orderDirn;
    }
    
    
    
}