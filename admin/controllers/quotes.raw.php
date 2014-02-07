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

// No direct access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.controlleradmin' );

/**
 * Vip Quotes quotes controller
 *
 * @package     VipQuotes
 * @subpackage  Component
  */
class VipQuotesControllerQuotes extends JControllerAdmin {
    
    /**
     * @var     string  The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_VIPQUOTES';
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Quote', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 * @return  void
	 * @since   3.0
	 */
	public function saveOrderAjax() {
	    
		// Get the input
		$pks     = $this->input->post->get('cid', array(), 'array');
		$order   = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the item
        try {
            $model->saveorder($pks, $order);
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));
        }
        
        jimport("itprism.response.json");
        $response = new ITPrismResponseJson(JText::_('COM_VIPQUOTES_SUCCESS'), JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
        $response->success();
        echo $response;
        
        JFactory::getApplication()->close();
		
	}
    
}