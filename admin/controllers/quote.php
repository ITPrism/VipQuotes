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

// No direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

/**
 * Quote controller class.
 *
 * @package		ITPrism Components
 * @subpackage	Vip Quotes
 * @since		1.6
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
        
        $itemId  = JRequest::getInt("id");
        $msg     = "";
        $link    = "";
        
        /***** Validate form data *****/
        $data    = JRequest::getVar('jform', array(), 'post', 'array');
        $model   = $this->getModel("Quote", "VipQuotesModel");
        /** @var $model VipQuotesModelQuote **/
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
            $this->defaultLink .= "&view=quote&layout=edit";
            
            if($itemId) {
                $this->defaultLink .= "&id=" . JRequest::getInt("id");
            } 
            return $this->setRedirect(JRoute::_($this->defaultLink, false));
        }
            
        try{
            
            $itemId = $model->save($validData);
                
        }catch(Exception $e){
            
            $itpSecurity = new ITPrismSecurity($e);
            $itpSecurity->alertMe();
            
            JError::raiseError(500, JText::_('ITP_ERROR_SYSTEM'));
            return false;
        
        }
        
        $msg  = JText::_('COM_VIPQUOTES_QUOTE_SAVED');
        $link = $this->prepareRedirectLink($itemId);
        
        $this->setRedirect(JRoute::_($link, false), $msg);
    
    }
    
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
    
    /**
     * Cancel operations
     *
     */
    public function cancel(){
        
        $msg = "";
        $this->setRedirect(JRoute::_($this->defaultLink . "&view=quotes", false), $msg);
    
    }
    
}