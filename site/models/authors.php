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

jimport('joomla.application.component.modellist');

class VipQuotesModelAuthors extends JModelList {
    
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
                'name', 'a.name',
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
        $value      = $params->get("authors_limit", $app->getCfg('list_limit', 20));
        $this->setState('list.limit', $value);
        
        $value      = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);
        
        // Filters
        
        // Ordering
        $filterOrdering = $app->getUserStateFromRequest($this->context."filter.ordering", "filter_author_ordering", 0);
        $this->setState('filter.ordering', $filterOrdering);
        
        // Ordering state
        $this->prepareOrderingState($filterOrdering);
        
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
                'a.id, a.name, a.alias, a.bio, ' .
                'a.thumb, '.$query->concatenate(array("a.id", "a.alias"), ":") . ' AS slug '
            )
        );
        $query->from($db->quoteName('#__vq_authors') .' AS a');

        // Filter by state
        $query->where('a.published = 1');

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));
        $query->group("a.id");

        return $query;
    }
    
    /**
     * 
     * Prepare a string used for ordering results
     * @param integer $filterOrdering
     */
    protected function prepareOrderingState($filterOrdering) {
        
        $listOrder = 'ASC';
        
        switch($filterOrdering) {
            
            case 1: // Ordering reversed
                $orderCol  = "a.name";
                break;
                
            case 2: // Name reversed
                $orderCol  = "a.hits";
                $listOrder = "DESC";
                break;
                
            default: // Ordering
                $orderCol = "a.ordering";
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