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

// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.application.component.controller' );

/**
 * Vip Quotes export controller
 *
 * @package     VipQuotes
 * @subpackage  VipQuotes
  */
class VipQuotesControllerExport extends JControllerLegacy {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Export', $prefix = 'VipQuotesModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function download() {
        
        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $type  = $this->input->get->getCmd("type");
        $model = $this->getModel();
        
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.archive');
        jimport('itprism.xml.simple');
        
        try {
            
            $output      = $model->getData();
            $date        = new JDate();
            $fileName    = "vipquotes_".$date->format("Y-m-d").".xml";
                    
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_VIPQUOTES_ERROR_SYSTEM'));
        }
        
        $tmpFolder   = JPath::clean( $app->getCfg("tmp_path") );
        
        $archiveFile = JFile::stripExt($fileName).".zip";
        $destination = $tmpFolder.DIRECTORY_SEPARATOR.$archiveFile;
        
        // compression type
        $zipAdapter   = JArchive::getAdapter('zip'); 
        $filesToZip[] = array(
        	'name' => $fileName, 
        	'data' => $output
        ); 
        
        $zipAdapter->create($destination, $filesToZip, array());
        
        $filesize = filesize($destination);
        
        JResponse::setHeader('Content-Type', 'application/octet-stream', true);
        JResponse::setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        JResponse::setHeader('Content-Transfer-Encoding', 'binary', true);
        JResponse::setHeader('Pragma', 'no-cache', true);
        JResponse::setHeader('Expires', '0', true);
        JResponse::setHeader('Content-Disposition', 'attachment; filename='.$archiveFile, true);
        JResponse::setHeader('Content-Length', $filesize, true);
        
        $doc = JFactory::getDocument();
        $doc->setMimeEncoding('application/octet-stream');

        echo JFile::read($destination);
        
    }
    
}