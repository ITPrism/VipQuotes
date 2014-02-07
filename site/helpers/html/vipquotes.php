<?php
/**
 * @package      VipQuotes
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
	
	public static function information($item, $categoryTitle, $tmpValue, $displayPublisher, $socialProfile = "") {
	
	    $html = array();
	     
	    // Display category
        $category = '<a href="'. JRoute::_(VipQuotesHelperRoute::getCategoryRoute($item->catid).$tmpValue).'">'.$categoryTitle.'</a>';
        $html[]   = JText::sprintf("COM_VIPQUOTES_IN", $category);
	    
	    // Display date
        $date   = JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC3'));
        $html[] = JText::sprintf("COM_VIPQUOTES_ON", $date);
	    
	    if($displayPublisher) {
	        if(!empty($socialProfile)){
	            $html[] = JText::sprintf("COM_VIPQUOTES_BY", $socialProfile);
	        } else {
	            $html[] = JText::sprintf("COM_VIPQUOTES_BY", $item->publisher);
	        }
	    }
	    
	    // Prepare hits
        $hits = " | " .JText::sprintf("COM_VIPQUOTES_HITS", $item->hits);
	    
        // Prepare output
        $output = JText::_("COM_VIPQUOTES_PUBLISHED_"). implode(" ", $html);
	    
	    return $output.$hits;
	}
	
	public static function socialProfileLink($link, $name, $options = array()) {
	
	    if(!empty($link)) {
	
	        $targed = "";
	        if(!empty($options["target"])) {
	            $targed = 'target="'.JArrayHelper::getValue($options, "target").'"';
	        }
	
	        $output = '<a href="'.$link.'" '.$targed.'>'.htmlspecialchars($name, ENT_QUOTES, "UTF-8").'</a>';
	
	    } else {
	        $output = htmlspecialchars($name, ENT_QUOTES, "utf-8");
	    }
	
	    return $output;
	}
}
