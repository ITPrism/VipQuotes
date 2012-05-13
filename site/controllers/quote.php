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

jimport('joomla.application.component.controllerform');

/**
 * @package		VipQuotes
 * @subpackage	com_vipquotes
 * @since		2.5
 */
class VipQuotesControllerQuote extends JControllerForm {
    /**
     * @since	1.6
     */
    protected $view_item = 'form';
    
	/**
     * Method to get a model object, loading it if required.
     *
     * @param	string	$name	The model name. Optional.
     * @param	string	$prefix	The class prefix. Optional.
     * @param	array	$config	Configuration array for model. Optional.
     *
     * @return	object	The model.
     * @since	1.5
     */
    public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        
        return $model;
    }
    
    /**
     * Method to cancel an edit.
     *
     * @param	string	$key	The name of the primary key of the URL variable.
     *
     * @return	Boolean	True if access level checks pass, false otherwise.
     * @since	1.6
     */
    public function cancel($key = 'quote_id') {
        
        // Redirect to the return page.
        $this->setRedirect($this->getReturnPage());
        
        return true;
    }
    
    /**
     * Method to save a record.
     *
     * @param	string	$key	The name of the primary key of the URL variable.
     * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return	Boolean	True if successful, false otherwise.
     * @since	1.6
     */
    public function save($key = null, $urlVar = 'quote_id') {
        
        // Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
 
		// Get the data from the form POST
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		
		// Initialise variables.
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
 
        $model   = $this->getModel("Form", "VipQuotesModel");
        /** @var $model VipQuotesModelForm **/
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        
        // Check for validation errors.
        if($validData === false){
            JError::raiseWarning(500, $model->getError());
            $returnPage = $this->getReturnPage();
            $this->setRedirect(JRoute::_($returnPage, false));
            return false;
        }

        if($validData['id']) {
            $model->validateQuote($validData['id'], $user->id);
        }
        $validData['user_id']     = $user->id;
        
        $componentParams    = JComponentHelper::getParams('com_vipquotes');
        if($componentParams->get("autoPublishing")) {
            $validData['published']   =  1;
        } else {
            $validData['published']   =  0;
        }
            
        $model->save($validData);
        
        $returnPage = $this->getReturnPage();
        $this->setRedirect(JRoute::_($returnPage, false), JText::_('COM_VIPQUOTES_QUOTE_SAVED'));
        
        return true;
    }
    
    /**
     * Get the return URL.
     *
     * If a "return" variable has been passed in the request
     *
     * @return	string	The return URL.
     * @since	1.6
     */
    protected function getReturnPage() {
        $return = JRequest::getVar('return', null, 'default', 'base64');
        
        if (empty($return) || ! JUri::isInternal(base64_decode($return))) {
            return JURI::base();
        } else {
            return base64_decode($return);
        }
    }

}
