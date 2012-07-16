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

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Quote controller class.
 *
 * @package		ITPrism Components
 * @subpackage	VipQuotes
 */
class VipQuotesControllerQuote extends JControllerForm {
    
    // Check the table in so it can be edited.... we are done with it anyway
    private $defaultLink = 'index.php?option=com_vipquotes';
    
    /**
     * Save an item
     *
     */
    public function save(){
        
        JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $msg     = "";
        $link    = "";
        $itemId  = $app->input->getInt("id");
        
        // Validate form data
        $data    = $app->input->getVar('jform', array(), 'post', 'array');
        $model   = $this->getModel();
        /** @var $model VipQuotesModelQuote **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            JError::raiseError(500, $model->getError());
        }
            
        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        
        // Check for validation errors.
        if($validData === false){
            $this->setMessage($model->getError(), "notice");
            
            $link = $this->prepareRedirectLink($itemId);
            $this->setRedirect(JRoute::_($link, false));
            return;
        }
       
        // Load component parameters.
        $params  = JComponentHelper::getParams('com_vipquotes');
        
        // Check for duplications
        if($params->get("checkQuotes")) {
                
            $quote          = JArrayHelper::getValue($data, "quote");
            
            if($model->hasDuplication($quote, $itemId)) {
                
                $this->setMessage(JText::_('COM_VIPQUOTES_ERROR_DUPLICATION'), "notice");
                
                $link = $this->prepareRedirectLink($itemId);
                $this->setRedirect(JRoute::_($link, false));
                return;
            }
        }
            
        try{
            $itemId = $model->save($validData);
        } catch(Exception $e){
            throw new Exception( JText::_('ITP_ERROR_SYSTEM'), 500);
        }
        
        $this->setMessage(JText::_('COM_VIPQUOTES_QUOTE_SAVED'), "message");

        $link = $this->prepareRedirectLink($itemId);
        $this->setRedirect(JRoute::_($link, false));
    
    }
    
    /**
     * Cancel operations
     *
     */
    public function cancel(){
        
        $link = $this->prepareRedirectLink();
        $this->setRedirect(JRoute::_($link, false));
    
    }
    
    /**
     * 
     * Prepare return link
     * @param integer $itemId
     */
    protected function prepareRedirectLink($itemId = 0) {
        
        $task = $this->getTask();
        $link = $this->defaultLink;
        
        // Prepare redirection
        switch($task) {
            case "apply":
                $link .= "&view=quote&layout=edit";
                if(!empty($itemId)) {
                    $link .= "&id=" . (int)$itemId; 
                }
                break;
                
            case "save2new":
                $link .= "&view=quote&layout=edit";
                break;
                
            default:
                $link .= "&view=quotes";
                break;
        }
        
        return $link;
    }
    
}