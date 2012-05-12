<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php 
    if (!empty($items)) {
        $i = 0;
        foreach($items as $item) {
            $item->quote = strip_tags($item->quote);
            $i++;
            if ($qMarks) {
            	$item->quote = '"' . $item->quote . '"';
            }
            
            echo "<p>$i. $item->quote</p>"; 
            
            if($showAuthor) {
                echo "<p><strong>$item->author</strong></p>";
            }
        }
    }
?>
