<?php
defined('_JEXEC') or die;
jimport('joomla.application.categories');

class VipQuotesCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table']     = '#__vq_quotes';
		$options['extension'] = 'com_vipquotes';
		parent::__construct($options);
	}
	
	public static function getCategories() {
	    
	    $db = JFactory::getDbo();
	    /** @var $db JDatabaseMySQLi **/
	    
	    $query = $db->getQuery(true);
	    $query
	        ->select("id, title")
	        ->from("#__categories")
	        ->where($db->quoteName(extension). " = " . $db->quote("com_vipquotes"));
	    
	    $db->setQuery($query);
	    return $db->loadAssocList("id", "title");
	}
	
}