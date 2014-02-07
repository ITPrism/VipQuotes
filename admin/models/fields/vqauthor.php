<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      VipQuotes
 * @subpackage   Component
 * @since       1.6
 */
class JFormFieldVqAuthor extends JFormFieldList {
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'vqauthor';
    
    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions(){
        
        $published = (int)JArrayHelper::getValue($this->element, "published");
        
        // Get authors.
        jimport("vipquotes.filter.options");
        $filters = VipQuotesFilterOptions::getInstance(JFactory::getDbo());
        $options = $filters->getAuthors($published);
        
        $displayRoot = (!empty($this->element["display_root"])) ? true : false;
        if($displayRoot) {
            array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_VIPQUOTES_SELECT_AUTHOR'), 'value', 'text'));
        }
        
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        
        return $options;
    }
}
