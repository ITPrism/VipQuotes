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

jimport('joomla.application.component.modellist');

class VipQuotesModelCategories extends JModelList {
    
    protected $items  = null;
    protected $parent = null;
    
    /**
     * Constructor.
     *
     * @param   array   An optional associative array of configuration settings.
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array()){
        if(empty($config['filter_fields'])){
            $config['filter_fields'] = array();
        }
        
        parent::__construct($config);
    }
    
    public function getItems( $recursive = false ){
        
        if (!count($this->items)) {
			
			// import Joomla Categories library
			//if you forget this -> Fatal error: Class 'JCategories' not found in ...
			jimport( 'joomla.application.categories' );

			$options = array();

			$categories = JCategories::getInstance('VipQuotes', $options);
			$this->parent = $categories->get('root');

			if (is_object($this->parent)) {
				$this->items = $this->parent->getChildren($recursive);
			}
			else {
				$this->items = false;
			}
		}

		return $this->items;
		
    }
}