jQuery(document).ready(function() {

	var elementFilterAuthor = jQuery("#filter_author_ordering");
	if(elementFilterAuthor.length > 0) {
		jQuery("#filter_author_ordering").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
	
}); 