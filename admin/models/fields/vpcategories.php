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
defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      ITPrism Components
 * @subpackage   Vip Quotes
 * @since       1.6
 */
class JFormFieldVpCategories extends JFormFieldList {
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'VpCategories';
    
    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions(){
        
        // Initialize variables.
        $options = array();
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS value, a.name AS text');
        $query->from('#__vp_categories AS a');
        
        // Get the options.
        $db->setQuery($query);
        
        $options = $db->loadAssocList();
        
        // Check for a database error.
        if($db->getErrorNum()){
            JError::raiseWarning(500, $db->getErrorMsg());
        }
        
        array_unshift($options, JHTML::_('select.option', '0', '- '.JText::_('Select a category').' -', 'value', 'text'));
        
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        
        return $options;
    }
}
