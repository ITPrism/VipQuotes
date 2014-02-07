jQuery(document).ready(function() {
	
	var elementFilterAuthor = jQuery("#filter_author");
	if(elementFilterAuthor.length > 0) {
		jQuery("#filter_author").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterCategory = jQuery("#filter_category");
	if(elementFilterCategory.length > 0) {
		jQuery("#filter_category").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterUser = jQuery("#filter_user");
	if(elementFilterUser.length > 0) {
		jQuery("#filter_user").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterOrdering = jQuery("#filter_ordering");
	if(elementFilterOrdering.length > 0) {
		jQuery("#filter_ordering").chosen().change(function(event){
			jQuery("#vq-filters-form").submit();
		});
	}
	
}); 