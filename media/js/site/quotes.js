jQuery(document).ready(function() {
	
	var elementFilterAuthor = jQuery("#filter_author");
	if(elementFilterAuthor.length > 0) {
        elementFilterAuthor.chosen().change(function(){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterCategory = jQuery("#filter_category");
	if(elementFilterCategory.length > 0) {
        elementFilterCategory.chosen().change(function(){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterUser = jQuery("#filter_user");
	if(elementFilterUser.length > 0) {
        elementFilterCategory.chosen().change(function(){
			jQuery("#vq-filters-form").submit();
		});
	}
	
	var elementFilterOrdering = jQuery("#filter_ordering");
	if(elementFilterOrdering.length > 0) {
        elementFilterCategory.chosen().change(function(){
			jQuery("#vq-filters-form").submit();
		});
	}
}); 