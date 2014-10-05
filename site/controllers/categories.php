<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * VipQuotes categories Controller
 *
 * @package     ITPrism Components
 * @subpackage  VipQuotes
 */
class VipQuotesControllerCategories extends JControllerLegacy
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Category', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
