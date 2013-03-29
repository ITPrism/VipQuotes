<?php
/**
* @package      ITPrism Components
* @subpackage   Gamification
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Gamification is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_PLATFORM') or die;

/**
 * This class contains common methods and properties 
 * used in work with forms.
 */
class VipQuotesControllerAdminForm extends JControllerForm {
    
    /**
     * A default link to the extension
     * @var string
     */
    protected $defaultLink = 'index.php?option=com_vipquotes';
    
    /**
     * @var     string  The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_VIPQUOTES';
    
    /**
     * 
     * This method prepare a link where the user will be redirected 
     * after action he has done.
     * @param integer $itemId
     */
    protected function prepareRedirectLink($itemId = null, $urlVar = "id", $options = array() ) {
        
        $task = $this->getTask();
        $link = $this->defaultLink;
        
        // Redirect to URI provided by users
        if(!empty($options)) {
            
            // Use option provided by user
            if(!empty($options["force_option"])) {
                $link = "index.php?option=". $options["force_option"];
            }
            
            // Use view provided by user
            if(!empty($options["force_view"])) {
                $link .= "&view=" . $options["force_view"];
            }
            
            // Use task provided by user
            if(!empty($options["force_task"])) {
                $link .= "&task=" . $options["force_task"];
            }
            
            if(!empty($itemId)) {
                $link .= $this->getRedirectToItemAppend($itemId, $urlVar);
            } else {
                $link .= $this->getRedirectToListAppend();
            }
            
            return $link;
        }
        
        // Prepare redirection
        switch($task) {
            case "apply":
                $link .= "&view=".$this->view_item . $this->getRedirectToItemAppend($itemId, $urlVar);
                break;
                
            case "save2new":
                $link .= "&view=".$this->view_item . $this->getRedirectToItemAppend();
                break;
                
            default:
                $link .= "&view=".$this->view_list . $this->getRedirectToListAppend();
                break;
        }
        
        return $link;
    }
    
    /**
     * This method does cancel action
     */
    public function cancel(){
        $this->setRedirect(JRoute::_($this->defaultLink . "&view=".$this->view_list. $this->getRedirectToListAppend(), false));
    }
    
}

