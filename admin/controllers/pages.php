<?php
/**
 * @package      VipQuotes
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * VipQuotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport("itprism.controller.admin");

/**
 * VipQuotes Facebook Pages Controller
 *
 * @package     VipQuotes
 * @subpackage  VipQuotes
  */
class VipQuotesControllerPages extends ITPrismControllerAdmin {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Page', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function connect() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        // Get parameters
        $params = JComponentHelper::getParams($this->option);
        
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
        
        $uri         = JUri::getInstance();
        
        // Generate the link that will return the user back to administration.
        $redirectUrl = $uri->getScheme()."://".$uri->getHost().$uri->getPath()."?option=com_vipquotes&view=pages";
        
        $loginUrl    = $facebook->getLoginUrl(
            array(
                'scope'         => 'manage_pages',
                'redirect_uri'  => $redirectUrl
            )
        );
        
        $this->setRedirect($loginUrl);
    }
    
    /**
     * 
     * Get information about Facebook pages
     */
    public function update() {
        
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        // Get parameters
        $params = JComponentHelper::getParams($this->option);
        
        $redirectOptions = array(
            "view" => $this->view_list
        );
        
        $facebook = new Facebook(array(
            'appId'      => $params->get("fbpp_app_id"),
            'secret'     => $params->get("fbpp_app_secret"),
            'fileUpload' => false
        ));
        
        $facebookUserId = $facebook->getUser();
        
        if(!$facebookUserId) {
            $this->displayNotice(JText::_("COM_VIPQUOTES_ERROR_FACEBOOK_NOT_CONNECT"), $redirectOptions);
            return;
        }
        
        // Get a model
        $model = $this->getModel();
        /** @var $model VipQuotesModelPage **/
        
        try {
            $model->update($facebookUserId, $facebook);
        } catch ( Exception $e ) {
            $this->displayError(JText::_("COM_VIPQUOTES_ERROR_FACEBOOK"), $redirectOptions);
            return;
        }
        
        $this->displayMessage(JText::_("COM_VIPQUOTES_FACEBOOK_PAGES_UPDATED"), $redirectOptions);
        
    }
    
}