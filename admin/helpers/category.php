<?php
defined('_JEXEC') or die;
jimport('joomla.application.categories');

class VipquotesCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__vq_quotes';
		$options['extension'] = 'com_vipquotes';
		parent::__construct($options);
	}
	
	public static function getCategories() {
	    
	    $db = JFactory::getDbo();
	    /** @var $db JDatabaseMySQLi **/
	    
	    $query = "
	    	SELECT
	    		id, 
	    		title
	    	FROM
	    		`#__categories`
	    	WHERE
	    		`extension` = " . $db->quote("com_vipquotes");
	    
	    $db->setQuery($query);
	    return $db->loadAssocList("id","title");
	}
}