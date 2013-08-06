<?php
/**
 * @package      Vip Quotes
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Vip Quotes is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * VipQuotes Html Helper
 *
 * @package		Vip Quotes
 * @subpackage	Components
 * @since		1.6
 */
abstract class JHtmlVipQuotes {
    
    /**
     * @var   array   array containing information for loaded files
     */
    protected static $loaded = array();
    
	public static function boolean($value) {
	    
	    if(!$value) { // unpublished
		    $title  = "JUNPUBLISHED";
		    $class  = "unpublish";
	    } else {
	        $title  = "JPUBLISHED";
	        $class  = "ok";
	    }
		
		$html[] = '<a class="btn btn-micro" rel="tooltip" ';
		$html[] = ' href="javascript:void(0);" ';
		$html[] = ' title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
		$html[] = '<i class="icon-' . $class . '"></i>';
		$html[] = '</a>';
		
		return implode($html);
	}
    
	public static function authorsQuotesNumber($id, $display) {
	     
	    if(!$display) {
	        return "";
	    }
	    
	    $published = 1;
	    
	    $authorsQuotesNumber = VipQuotesHelper::getAuthorsQuotesNumber($published);
	    $number = JArrayHelper::getValue($authorsQuotesNumber, $id, 0, "int");
	    
	    return "(".$number.")";
	}
	
	public static function categoryQuotesNumber($id, $display) {
	
	    if(!$display) {
	        return "";
	    }
	     
	    $published = 1;
	    
	    $categoriesQuotesNumber = VipQuotesHelper::getCategoryQuotesNumber($published);
	    $number = JArrayHelper::getValue($categoriesQuotesNumber, $id, 0, "int");
	     
	    return "(".$number.")";
	}
	
	
	public static function authorImage($item, $params, $tmplValue) {
	
	    $html = array();
	    
	    if(!$item->thumb) {
	        
	        if(!$params->get("images_display_default", 1)) {
	            
	            return "";
	            
	        } else {

	            $ownImage = $params->get("images_own_picture");
	            
	            $html[] = '<a href="'. JRoute::_(VipQuotesHelperRoute::getAuthorRoute($item->slug).$tmplValue).'">';
	            
	            if(!$ownImage) {
	               $html[] = '<img src="media/com_vipquotes/images/no_image.png" />';
	            } else {
	                $html[] = '<img src="'.$ownImage.'" />';
	            }
	            
	            $html[] = '</a>';
	        }
	        
	    } else {
	        
	        $html[] = '<a href="'. JRoute::_(VipQuotesHelperRoute::getAuthorRoute($item->slug).$tmplValue).'">';
	        $html[] = '<img src="'.$params->get("images_directory")."/".$item->thumb.'" alt="'.addslashes(htmlentities($item->name, ENT_QUOTES, "UTF-8")).'" />';
	        $html[] = '</a>';
	        
	    }
		   
	    return implode("\n", $html);
	}
	
	public static function information($item, $categoryTitle, $displayCategory, $displayDate, $displayPublisher, $displayHits, $tmpValue) {
	
	    $html = array();
	     
	    if($displayCategory AND !empty($categoryTitle)) {
	        $category = '<a href="'. JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->catid).$tmpValue).'">'.$categoryTitle.'</a>';
	        $html[]   = JText::sprintf("COM_VIPQUOTES_IN", $category);
	    }
	    
	    if($displayDate) {
	        $date   = JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC3'));
	        $html[] = JText::sprintf("COM_VIPQUOTES_ON", $date);
	    }
	    
	    if($displayPublisher) {
	        $html[] = JText::sprintf("COM_VIPQUOTES_BY", $item->publisher);
	    }
	    
	    $hits = "";
	    if($displayHits) {
	        if(!empty($html)) {
	            $hits = " | " .JText::sprintf("COM_VIPQUOTES_HITS", $item->hits);
	        } else {
	            $hits = JText::sprintf("COM_VIPQUOTES_HITS", $item->hits);
	        }
	    }
	    
	    $output = "";
	    if(!empty($html)) {
	        $output = JText::_("COM_VIPQUOTES_PUBLISHED_"). implode(" ", $html);
	    }
	    
	    return $output.$hits;
	}
	
	/**
	 * Include Twitter Bootstrap
	 */
	public static function bootstrap() {
	
	    // Only load once
	    if (!empty(self::$loaded[__METHOD__])) {
	        return;
	    }
	
	    // Check for disabled including.
	    $componentParams = JComponentHelper::getParams("com_vipquotes");
	    
	    $document = JFactory::getDocument();
	
	    if($componentParams->get("bootstrap_style_include", 1)) {
	        $document->addStylesheet(JURI::root().'media/com_vipquotes/css/site/bootstrap.min.css');
	    }
	    
	    self::$loaded[__METHOD__] = true;
	
	    return;
	
	}
	
}
