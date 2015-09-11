<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
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
 * @since        1.6
 */
class JFormFieldVqAuthor extends JFormFieldList
{
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
    protected function getOptions()
    {
        $options   = array(
            "state" => $this->element["state"]
        );

        // Get authors.
        $filters = VipQuotes\Filter\Options::getInstance(JFactory::getDbo());
        $options = $filters->getAuthors($options);

        $displayRoot = (!empty($this->element["display_root"])) ? true : false;
        if ($displayRoot) {
            array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_VIPQUOTES_SELECT_AUTHOR'), 'value', 'text'));
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
