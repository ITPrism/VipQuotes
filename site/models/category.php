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

class VipQuotesModelCategory extends JModelList {
    
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
            	'quote', 'a.quote', 
            	'catid', 'a.catid', 
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
        
        // Load the parameters.
        $params            = $app->getParams("com_vipquotes");
        $this->setState('params', $params);
        
        // Set limit
        $value             = $params->get("category_quotes_limit", $app->getCfg('list_limit', 20));
        $this->setState('list.limit', $value);
        
        // Set limitstart
        $value             = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);
        
        // Set the category id
        $this->setState('filter.catid', $app->input->getInt('id'));
        
        // Filters
        
        // Ordering
        $displeyFilterOrdering = $params->get("category_display_filter_ordering", 0);
        if($displeyFilterOrdering) {
            $filterOrdering = $app->getUserStateFromRequest($this->context."filter.ordering", "filter_ordering", 0);
        } else {
            $filterOrdering = $params->get("category_quotes_order_by", 0);
        }
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
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = ''){
        // Compile the store id.
        $id .= ':' . $this->getState('filter.saerch');
        $id .= ':' . $this->getState('filter.catid');
        
        return parent::getStoreId($id);
    }
    
    /**
     * Get the master query for retrieving a list of projects to the model state.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    public function getListQuery(){
        // Create a new query object.
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);
        
        // Select the required fields from the table.
        $query->select(
            $this->getState(
            'list.select', 
            'a.id, a.quote, a.hits, a.created, a.published, ' .
            'a.catid, a.ordering'
        ));
        
        $query->from('#__vq_quotes AS a');
        
        // Use article state if badcats.id is null, otherwise, force 0 for unpublished
        $query->where('a.published = 1');
        
        // Filter by a single or group of categories
        $categoryId = intval($this->getState('filter.catid'));
        if(!empty($categoryId)){
            $query->where('a.catid = ' . (int)$categoryId);
        }
        
        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));
        
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
            case 1:
                $orderCol  = "a.created";
                break;

            case 2:
                $orderCol  = "a.created";
                $listOrder = "DESC";
                break;

            case 3:
                $orderCol = "b.name";
                break;
                
            default:
                $orderCol = "a.ordering";
                break;
        }
        
        // Set the column using for ordering
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