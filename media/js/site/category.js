jQuery(document).ready(function() {

	var elementFilterAuthor = jQuery("#filter_author");
	if(elementFilterAuthor) {
		jQuery("#filter_author").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterUser = jQuery("#filter_user");
	if(elementFilterUser) {
		jQuery("#filter_user").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterOrdering = jQuery("#filter_ordering");
	if(elementFilterOrdering) {
		jQuery("#filter_ordering").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
}); 