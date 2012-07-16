<?php // no direct access
defined('_JEXEC') or die; ?>
<?php 
    if (!empty($items)) {
        foreach($items as $item) {
            echo "<div>$item->quote</div><br/>"; 
            if($params->get('display_author', 1)) {
                echo "<div><strong>$item->author</strong></div>";
                echo '<br />';
            }
            
        }
    }
?>